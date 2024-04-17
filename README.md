# PurpleCloudb Testphase

## Offensive Part

### Introduction
The company *PurpleCloud* is hosting a private cloud environment for some time now. The CISO of the organization has a gut feeling that some of the resources are misconfigured and that several employees are handling security relevant information not exactly careful. Because of that, the CISO wants you to perform a black-box penetration test targeting the *PurpleCloud* cloud environement with the objective to discover all misconfigurations and vulnerabilities. The catch is that the CISO has a very tight schedule since the company plans to move the webapplications into production very soon. This means you are given only one hour to complete the penetration test and find all vulnerabilities in the system. To get started you should take a look at the PaaS webapplication PurpleWiki...

### Rules
* The challenges are interconnected so you have to start with challenge 1 and follow the path from there.
* You are allowed to use tools but you don't actually need any to solve the challenges.
* Respect the time limit so we can correctly analyse your progress and compare it fairly with the other participants.
* You are allowed to look at the solutions if you are stuck to get further going but if you do that please state that you did in the questionnaire
* Due to the time limit, we also want to encourage you to look at the solutions if you are noticing you are taking too much time for a challenge
* To solve a challenge, you need to find to find the according flag(s)
* Please note, the flags aren't the only thing you want to find! In some challenges there are hidden hints which you probably need to solve the following challenges. 
  
### Offensive Challenges

#### Challenge 1: Blob Hunt
First, you should take a look at the static PaaS web application 'PurpleWiki'. 

Entry Point: https://thankful-island-0ae319403.5.azurestaticapps.net

Flag Format: PURPLE{...}

#### Challenge 2: FindMe.log
PurpleCloud hosts another web application in the environment. This one is called PurpleBlog and is using an IaaS service model.

EntryPoint: http://purpleblog.westeurope.cloudapp.azure.com:8080/

Flag Format: There are two flags in this challenge! The first one is the name of the API endpoint and the second one is the SSH private key.


## introduction def

## rules def




