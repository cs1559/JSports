# General Model 

### Leagues
The "League" is the highest node of the model hiearchy.  It essentially represents an organizational entity which is
managing/runing specific programs.  While the model can support multiple "leagues", it is not advisable to have more than ONE (1) record in the Leagues table.  The rationale is that the component itself, via the "options" menu option in the admin side of Joomla, can have organizational attributes set at the component level.

**NOTE:**  Maintaining a 'league' record within the component will be deprecated at some point in the future.

### Programs
A program represents a grouping of divisions and teams that will compete against each other within a given timeframe.  They could be defined based on a calendar year, a season (e.g. 2024 Spring Baseball, etc.) or thy can be defined based ona given day of the week (e.g. 2024 Monday Golf League).  

A "program" is a registerable entity with then component.  

### Divisions
A division represents a collection of individuals/teams that will be competing against each other.  There is a 1-to-many relationship between a program and the divisions within a program.  The component also supports the grouping of divisions.  An example is grouping divisions based on an AGE GROUP (e.g. 7U, 8U, etc.)

### Team(s)
The team represents an individual entity that will be competing.  The TEAM could also be an actual individual (in theory).


# Example

League:  Southwest Illinois Baseball

Program: 2025 Spring Baseball

- Division:   7U
-- Illinois Gators
-- Highland Bulldogs
- Division:   8U
-- Edwardsville Tigers


