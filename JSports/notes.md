#Notes#

##Release 1.1.1
1.  This release introduces the bulletins capability in both the site and admin side of the component.
2.  General code cleanup.  This includes elimination of unnecessary USE statements and joomla code that is being deprecated in Release 6.  An example is the getDbo function.  Also as part of the code cleanup, we are removing references to /JObject and JPagination in the comments (@var reference).
3.  Fixed redirect issue for game reset.  It defaulted to redirect to the home teams page vs. from the page the redirect was initiated from.
4.  Updated the MyTeams view (incl. UserService SQL to retrieve teams list.)