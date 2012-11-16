NOTE: Still on development! (Internship project :D)

Hostnet Code Quality Review Platform.
This Symfony bundle can be used to check the code quality of a diff
through code quality metric tools like PMD, PHPMD and JSLint.

Features:
---------
- Manual SVN / Git Diff insert on the CLI and get feedback from the installed and configured code quality metric tools.
<br>In a Symfony2 project you can execute the following command:
<br>Input:   php app/console cq:processDiff:localDiff path_to_diff repository
<br>Example: php app/console cq:processDiff:localDiff path/to/diff repository
<p></p>
- Process a Review Board review request based on the review request id and get feedback on the CLI.
<br>Input:   php app/console cq:processDiff:RBDiff review_request_id [--diff_revision|-r]
<br>Example: php app/console cq:processDiff:RBDiff        11                 -r 2
<p></p>
- Process a Review Board review request based on the review request id and send the feedback to the review request on Review Board
  by adding a review with comments to the review request.
<br>Input:   php app/console cq:processDiff:RBDiff review_request_id [--publish_empty|-p] [--line_context|-c] [--line_limit|-l]
<br>Example: php app/console cq:processDiff:RBDiff       12345           -s true               -c 0               -l 25
<p></p>
- Process all the pending review requests on Review Board and send the feedback to each review request by adding a review with
  comments to each review request. This command creates a temp file in the temp dir which holds the last process date.
<br>Based on the date it checks which diffs already got processed and which didn't. Basically, if you automate this command with a cronjob
	all the new diffs on Review Board will be processed.
<br>Input:   php app/console cq:processAllNewDiffs [--publish_empty|-p] [--line_context|-c] [--line_limit|-l]
<br>Example: php app/console cq:processAllNewDiffs       -s true               -c 0              -l 25

Requirements:
-------------
- Manual installation and configuration of code quality metric tools 
  like PMD, PHPMD and JSLint
- Manual set the required settings in your own Symfony2 project's app/config parameters settings:
<p></p>
  hostnet_code_quality:
    <br>scm:																			Which revision management system is used in order to parse the correct diff format.
    <br>temp_cq_dir_name:													The path of the directory where the temporary files are saved and deleted afterwards.
		<br>raw_file_url_mask_1:											The cgit raw file url mask to retrieve the original file part 1, example: 'http://cgit.google.com/cgit/'
		<br>raw_file_url_mask_2:											part 2 example: '/www.git/plain'
		<br>review_board_base_url:										The base url of the Review Board location, example: 'http://reviews.google.com'
		<br>review_board_username:										Username of the Review Board user that will post all the review violation comments.
		<br>review_board_password:										Password of the Review Board user that will post all the review violation comments.
		<br>review_board_auto_shipit:									If diffs with zero violations should get an auto shipit ('true' or 'false').
		<br>review_board_previous_process_date_file:	Name of the temp Review Board previous process date, 'review_board_previous_process_date' for example.

Future Features:
----------------
- Past diff per file comparison
- Personal top 10 metric warnings chart
- Register a user's diff metric results compared to the original files
- Possibility to import/process historical git logs
- An overview of the past code quality results/grades
- A top-score chart filterable on department/team/project

##### Hostnet Recruitment ######
Op zoek naar een baan in de Software Engineering?
Hostnet is nog op zoek naar nieuwe ontwikkelaars.

Kijk voor meer informatie op:
###### http://www.hostnet.nl/vacatures/vacature-frontend-developer ######
###### http://www.hostnet.nl/vacatures/vacature-back-end-developer ######

<!-- ===============================================================================
*        Op zoek naar een baan in de Software Engineering?										*
*        Hostnet is nog op zoek naar nieuwe ontwikkelaars.										*
*																																							*
*   Kijk voor meer informatie op:                                             *
*   https://www.hostnet.nl/vacatures/vacature-software-engineer-programmeur   *
============================================================================== -->