#Notes#

##Release 1.1.1
1.  This release introduces the bulletins capability in both the site and admin side of the component.
2.  General code cleanup.  This includes elimination of unnecessary USE statements and joomla code that is being deprecated in Release 6.  An example is the getDbo function.  Also as part of the code cleanup, we are removing references to /JObject and JPagination in the comments (@var reference).
3.  Fixed redirect issue for game reset.  It defaulted to redirect to the home teams page vs. from the page the redirect was initiated from.
4.  Updated the MyTeams view (incl. UserService SQL to retrieve teams list.)

##Release 1.2
1.  Changed all calls of Factory::getUser to the UserService::getUser function (wrapper).  This change was to code on both the admin and site side of the component.
2.  Changed Factory::getDocument to Factory::getApplication()->getDocument();
3.  Removed admin/services/LeagueService class.
4.  Cleaned up several modules to eliminate unnecessary code.
5.  Fixed an issue with the Opponents Team List when entering a game score.  The teams current division of teams were duplicated and showing in the "outside division" section.  This required changes to the DivisionService and the TeamlistField classes.
6.  Updated TeamService queries to support parameter typing via Joomla's query buildree with BINDs
7.  Introduced the GAMES view that allows for the ability to query upcoming games and completed games.
8.  Introduced the POSTINGS view to provide a list of league wide bulletins
9.  Added appproval email for when a bulletin has been added or edited.
10. Support configuraton of maximum bulletin attachment size.