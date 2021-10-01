# DeckImportExport
nextcloud app that allows Deck import from Trello export to JSON file

# :rocket: Installation

To install it simply copy and paste the application into the apps directory of your nextcloud server instance. 
After that log into nextcloud and if you have admin rights navigate to your account > Apps and press the search button at the top.
Look for "Deck Import Export" and press enable. 

# :arrows_counterclockwise: Updating

After replacing the app folder, please make sure to install any new dependencies and disable then enable the app once more.

# :arrow_forward: Usage

Download Trello board to json file and add the file to Nextcloud. When both this app Deck are installed, for any .json file you have in your list, the three dot menu will offer a new action "Import to Deck". Click on it to start. When import completes, you will get a notification with a link to the new board. The board will have the same name as the original one on Trello.

## :warning: What is imported

* Labels
* Stacks
* Cards (title, id, description)
* Card checklists
* Card due dates
* Order of Stacks, Cards etc.
* Comments on cards

## :warning: What is NOT imported

* Creating archived cards: https://github.com/maxammann/trello-to-deck/issues/1
  I have over 2000 archived cards in my personal Trello. Right now Deck can not handle this amount. Therefore currenlty no archived cards are migrated!
* Assigning the correct people to cards
* Attributing people to comments. We are only adding the name of the person that added the comment within the comment, but all comments appear to have been added by current user doing the import
* Votes
* Background
* Cards of archived stacks
* Link/Reference to the original trello card (as comment) - not yet
* Attachments - not supported. In the past the Trello JSON export had for attachments a public URL, now in order to download the attachment Trello requires authentication
