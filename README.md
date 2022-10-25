# DeckImportFromTrello
nextcloud app that allows Deck import from Trello export to JSON file

# :rocket: Installation

To install it simply copy and paste the application into the apps directory of your nextcloud server instance. 
After that log into nextcloud and if you have admin rights navigate to your account > Apps and press the search button at the top.
Look for "Deck Import From Trello" and press enable. 

# :arrows_counterclockwise: Updating

After replacing the app folder, please make sure to install any new dependencies and disable then enable the app once more.

# :arrow_forward: Usage

- Export / Download the JSON-File from Trello
- Save it into your NextCloud "Files" Section
- Go to that file, and click on the 3 dots behind the Filename

![](https://user-images.githubusercontent.com/1778068/197753398-aa14a314-073b-4301-9f47-ed2805980543.png)

- Then you can import it.

## :warning: What is imported

* Labels
* Stacks
* Cards (title, id, description)
* Card checklists
* Card due dates
* Order of Stacks, Cards etc.
* Comments on cards
* (Partially) Attachments: added as comments with name and Trello URL
* (Partially) Card Members: added in the description of the card

## :warning: What is NOT imported

* Creating archived cards
* Cards of archived stacks
* Assigning the correct people to cards
* Attributing people to comments. We are only adding the name of the person that added the comment within the comment, but all comments appear to have been added by current user doing the import
* Votes
* Background
* (*) Attachments - not supported. In the past the Trello JSON export had for attachments a public URL, now in order to download the attachment Trello requires authentication. The app imports them as comments with the original link and who has access to the original attachment can access them from Deck.
* (*) Card Members - not supported. Trello does not export the users' emails, only the names so the app uses them in the description of the card.
