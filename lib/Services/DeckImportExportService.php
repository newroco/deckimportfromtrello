<?php

namespace OCA\DeckImportExport\Services;

use OCA\Deck\Service\AttachmentService;
use OCA\Deck\Service\BoardService;
use OCA\Deck\Service\CardService;
use OCA\Deck\Service\CommentService;
use OCA\Deck\Service\LabelService;
use OCA\Deck\Service\StackService;
use Httpful\Request;

class DeckImportExportService
{
    /**
     * @var BoardService
     */
    private $boardService;
    /**
     * @var AttachmentService
     */
    private $attachmentService;
    /**
     * @var
     */
    private $userId;

    /**
     * @var
     */
    private $board;
    /**
     * @var StackService
     */
    private $stackService;
    /**
     * @var LabelService
     */
    private $labelService;

    /**
     * @var
     */
    private $labels = [];

    /**
     * @var array
     */
    private $stacks = [];

    /**
     * @var array
     */
    private $cards = [];

    /**
     * @var array
     */
    private $members = [];

    /**
     * @var CardService
     */
    private $cardService;
    /**
     * @var CommentService
     */
    private $commentService;


    /**
     * DeckImportExportService constructor.
     */
    public function __construct(
        BoardService      $boardService,
        AttachmentService $attachmentService,
        StackService      $stackService,
        LabelService      $labelService,
        CardService       $cardService,
        CommentService    $commentService,
                          $userId
    )
    {
        $this->attachmentService = $attachmentService;
        $this->boardService = $boardService;
        $this->stackService = $stackService;
        $this->labelService = $labelService;
        $this->cardService = $cardService;
        $this->commentService = $commentService;
        $this->userId = $userId;
    }

    /**
     * @param $jsonFile
     */
    public function parseJsonAndImport($jsonFile)
    {
        $data = json_decode($jsonFile, true);

        $this->board = $this->createTheBoard($data['name']);

        $this->parseLabels($data['labels']);
        $this->parseStacks($data['lists']);
        $this->parseMembersName($data['members']);

        $this->parseCards($data['cards'], $data['checklists']);

        $this->parseComments($data['actions']);

        return $this->board;
    }

    /**
     * Parse trello labels
     *
     * @param $labels
     */
    private function parseLabels($labels)
    {
        foreach ($labels as $label) {
            $labelId = $label['id'];
            if ($label['name'] == '') {
                $title = 'Unnamed ' . $label['color'] . ' label';
            } else {
                $title = $label['name'];
            }
            switch ($label['color']) {
                case 'red':
                    $color = 'ff0000';
                    break;
                case 'yellow':
                    $color = 'ffff00';
                    break;
                case 'orange':
                    $color = 'ff6600';
                    break;
                case 'green':
                    $color = '00ff00';
                    break;
                case 'purple':
                    $color = '9900ff';
                    break;
                case 'blue':
                    $color = '0000ff';
                    break;
                case 'sky':
                    $color = '00ccff';
                    break;
                case 'lime':
                    $color = '00ff99';
                    break;
                case 'pink':
                    $color = 'ff66cc';
                    break;
                case 'black':
                    $color = '000000';
                    break;
                default:
                    $color = 'ffffff';
            }
            $this->labels[] = $this->createLabel($title, $color);
        }
    }

    /**
     * Parse stacks
     *
     * @param $stacks
     */
    private function parseStacks($stacks)
    {
        $order = 1;
        foreach ($stacks as $stack) {
            //if stack closed skip
            if ($stack['closed']) {
                continue;
            }
            $stackId = $stack['id'];
            $title = $stack['name'];
            $this->stacks[$stackId] = $this->createStack($title, $order);
            $order++;
        }
    }

    /**
     * Parse the cards
     *
     * @param $cards , $checklists
     */
    private function parseCards($cards, $checklists)
    {
        $checklistsMapper = [];

        foreach ($checklists as $checklist) {
            $checklistsMapper[$checklist['id']] = $checklist;
        }

        foreach ($cards as $card) {
            //check if card is closed or stack is closed
            if ($card['closed'] || !array_key_exists($card['idList'], $this->stacks)) {
                continue;
            }

            $id = $card['id'];
            $title = $card['name'];
            $description = $card['desc'];
            $newStackID = $this->stacks[$card['idList']];
            $dueDate = $card['due'];

            if (count($card['idMembers'])) {
                $description .= "\n Card was assigned to ";

                foreach ($card['idMembers'] as $key => $member) {
                    $card['idMembers'][$key] = $this->members[$member];
                }

                $description .= implode($card['idMembers'], ',');

                $description .= "\n";
            }

            if (count($card['idChecklists'])) {
                $description .= "## Checklists\n\n";

                foreach ($card['idChecklists'] as $idChecklist) {
                    $checklist = $checklistsMapper[$idChecklist];

                    $description .= '### ' . $checklist['name'] . "\n\n";

                    foreach ($checklist['checkItems'] as $checkItem) {
                        $description .= "- " . ($checkItem['state'] === 'complete' ? '[x]' : '[ ]') . ' ' . $checkItem['name'] . "\n";
                    }
                }
            }

            $order = $card['idShort'];

            $cardId = $this->createCard($title, $newStackID, $order, $description, $dueDate);

            $this->cards[$id] = $cardId;

//            $this->createAttachments($card, $cardId);
            $this->addAttachmentsAsComments($card, $cardId);
        }
    }

    /**
     * Set members names for usage in comments
     *
     * @param $members
     */
    private function parseMembersName($members)
    {
        foreach ($members as $member) {
            $this->members[$member['id']] = $member['fullName'];
        }
    }

    /**
     * Parse the comments
     *
     * @param $actions
     */
    private function parseComments($actions)
    {
        foreach ($actions as $action) {
            if ($action['type'] !== 'commentCard') {
                continue;
            }

            if (!isset($this->cards[$action['data']['card']['id']])) {
                continue;
            }

            $cardId = $this->cards[$action['data']['card']['id']];

            if (is_null($cardId)) {
                continue;
            }

            if (array_key_exists($action['idMemberCreator'], $this->members)) {
                $message = 'Comment by ' . $this->members[$action['idMemberCreator']] . ': ';
            } else {
                $message = 'Comment by Unknown: ';
            }

            $message .= $action['data']['text'];

            $this->createComment($cardId, $message, 0);
        }
    }

    /**
     * Create the board
     *
     * @param $boardName
     * @return \OCP\AppFramework\Db\Entity
     * @throws \OCA\Deck\BadRequestException
     */
    private function createTheBoard($boardName)
    {
        return $this->boardService->create($boardName, $this->userId, 'ff0000');
    }

    /**
     * Create new label
     *
     * @param $title
     * @param $color
     * @return \OCP\AppFramework\Db\Entity
     * @throws \OCA\Deck\BadRequestException
     * @throws \OCA\Deck\NoPermissionException
     * @throws \OCA\Deck\StatusException
     * @throws \OCP\AppFramework\Db\DoesNotExistException
     * @throws \OCP\AppFramework\Db\MultipleObjectsReturnedException
     */
    private function createLabel($title, $color)
    {
        return $this->labelService->create($title, $color, $this->board->id);
    }

    /**
     * Create stack
     *
     * @param $title
     * @param $order
     * @return mixed
     * @throws \OCA\Deck\BadRequestException
     * @throws \OCA\Deck\NoPermissionException
     * @throws \OCA\Deck\StatusException
     * @throws \OCP\AppFramework\Db\DoesNotExistException
     * @throws \OCP\AppFramework\Db\MultipleObjectsReturnedException
     */
    private function createStack($title, $order)
    {
        $stack = $this->stackService->create($title, $this->board->id, $order);
        return $stack->id;
    }

    /**
     * Create a new card
     *
     * @param $title
     * @param $stackID
     * @param $order
     * @param $description
     * @param null $duedate
     * @return \OCP\AppFramework\Db\Entity
     * @throws \OCA\Deck\BadRequestException
     * @throws \OCA\Deck\NoPermissionException
     * @throws \OCA\Deck\StatusException
     * @throws \OCP\AppFramework\Db\DoesNotExistException
     * @throws \OCP\AppFramework\Db\MultipleObjectsReturnedException
     */
    private function createCard($title, $stackID, $order, $description, $duedate = null)
    {
        $card = $this->cardService->create($title, $stackID, 'plain', $order, $this->userId, $description, $duedate);
        return $card->id;
    }

    /**
     * @param int $cardId
     * @param string $message
     * @param int $parentId
     * @return \OCP\AppFramework\Http\DataResponse
     * @throws \OCA\Deck\BadRequestException
     * @throws \OCA\Deck\NoPermissionException
     * @throws \OCA\Deck\NotFoundException
     */
    private function createComment(int $cardId, string $message, int $parentId)
    {
        return $this->commentService->create($cardId, $message, $parentId);
    }

    protected function addAttachmentsAsComments(array $card, int $cardId)
    {
        if (count($card['attachments']) === 0) {
            return;
        }

        $message = "Trello Attachments \n\n";

        foreach ($card['attachments'] as $attachment) {
            if (!$attachment['isUpload']) {
                continue;
            }

            $message .= " - " . $attachment['name'] . " (". $attachment['url'] . ")\n";
        }

        $this->createComment($cardId, $message, 0);
    }

//    protected function createAttachments(array $card, int $cardId)
//    {
//        try {
//            if (count($card['attachments'])) {
//                foreach ($card['attachments'] as $attachment) {
//                    if ( ! $attachment['isUpload']) {
//                        continue;
//                    }
//
//                    $context = stream_context_create([
//                        'http' => [
//                            'follow_location' => false
//                        ]
//                    ]);
//
//                    $json = file_get_contents($attachment['url'], false, $context);
////                    $contents = json_decode($json, true);
//
//                    $this->attachmentService->create($cardId, 'deck_file', $json);
//                }
//            }
//        } catch (\Exception $exception) {
//            //
//        }
//    }
}
