# The project is a CRUD that can write into database notes(tasks) that have a status 
**The database should have three tables.**
| Status| |                            
| :---: | :---: | 
| id | int(11)| 
| title | varcahr(64)| 


| Note| |                            
| :---: | :---: | 
| id | int(11)| 
| title| varcahr(255)| 
| priority| varcahr(255)|  
| note| text|   
| status_id | int(11)| 
***The connection between tables*** | **notes.statuse_id *------> statuses.id***

| Users| |                            
| :---: | :---: | 
| id | int(11)| 
| email| varcahr(64)| 
| pass| password(128)|   

**The project has several specifications:**
- add, deleate and modify data from database. 
- notes have a field (drop down) from which it is possible to select a mechanic
- filtration from created notes by status
- sorting status by title
- sign up, sign in and sign out funtionality
- notes field for note with redactor summernode
- responsive design of project
- protection agains SQL injection


### project was made with Symfony framework
