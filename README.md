NOTE: Still on development! (Internship project :D)

Hostnet Code Quality Review Platform.
This Symfony bundle can be used to check the code quality of a diff
through code quality metric tools like PMD, PHPMD and JSLint.

Features:
---------
- Manual SVN / Git Diff insert on the CLI and get feedback from the installed and configured code quality metric tools.
  In a Symfony2 project you can execute the following command:
  Input:   php app/console cq:processDiff:localDiff path_to_diff [--register|-r]
  Example: php app/console cq:processDiff:localDiff path/to/diff     -r true
  Registration is for the overviews, although they will be added in the future.

Requirements:
-------------
- Manual installation and configuration of code quality metric tools 
  like PMD, PHPMD and JSLint
- Manual set the required settings in your own Symfony2 app/config settings:
  hostnet_code_quality:
    scm:                Which revision management system is used in order to parse the correct diff format.
    raw_file_url_mask:  The base url which will be used to retrieve the original file in order to compare it with
                        the diff file. In the future I will add multiple retrieval implementations for this.
    temp_cq_dir_name:   The path of the directory where the temporary files are saved and deleted afterwards.

Future Features:
----------------
- Manual Review Board review request id insert on the CLI and get feedback on a diff of that supplied review request.
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

<!-- ===================================================================
*        Op zoek naar een baan in de Software Engineering?        *
*        Hostnet is nog op zoek naar nieuwe ontwikkelaars.        *
*                                                                 *
*   Kijk voor meer informatie op:                                 *
*   http://www.hostnet.nl/vacatures/vacature-frontend-developer   *
*   http://www.hostnet.nl/vacatures/vacature-back-end-developer   *
=================================================================== -->