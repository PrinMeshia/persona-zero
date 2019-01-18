![medevia logo ](http://medevia.fr/images/logo.png)		
# Welcome to Medevia 


old persona alias persona zero

This framework is under construction 

## demo : later 

## Requirement

 - PHP >= 7.2.*
 - Twig
 - PHP-DI

### Configuration
	 - Main configuration -> Config/System/config.json

	 to add your configuration, edit it or create another file with extension .json

### environment
	The environment is managed via the env.json configuration file
	Each node must match a file in the Environment folder
	exemple of environment configuration file

	```
		{
			"debug": true, 
			"database": {
				"host": "persona.zero",
				"name": "persona",
				"charset": "",
				"user": "root",
				"pass": "",
				"prefix": "",
				"cache": true,
				"cache_time": 60
			},
			"rootfolder" : ""
		}
	```
rootfolder may contain tha default folder of website
example : 
```
if url = http://localhost/persona
rootfolder = persona/
```
### Create route :
	 - Edit route.yml in the config folder

## In progress
	ORM improvment
	User security

	
```
