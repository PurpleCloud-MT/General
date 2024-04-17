# PurpleCloud Testphase

## Questionnaire
After completion of both parts, please take the time to visit the following link and answer the questions there:
https://docs.google.com/forms/d/e/1FAIpQLSfMdUnj3i6Rx_q78RpIEV70VlTUnNnY7sx7QgqAKL3jwvAKkQ/viewform?usp=sf_link


## Offensive Part


### Introduction
The company *PurpleCloud* is hosting a private cloud environment for some time now. The CISO of the organization has a gut feeling that some of the resources are misconfigured and that several employees are handling security relevant information not exactly careful. Because of that, the CISO wants you to perform a black-box penetration test targeting the *PurpleCloud* cloud environement with the objective to discover all misconfigurations and vulnerabilities. The catch is that the CISO has a very tight schedule since the company plans to move the webapplications into production very soon. This means you are given only one hour to complete the penetration test and find all vulnerabilities in the system. To get started you should take a look at the PaaS webapplication PurpleWiki...


### Rules
* The challenges are interconnected so you have to start with challenge 1 and follow the path from there.
* You are allowed to use tools but you don't actually need any to solve the challenges.
* Respect the time limit so we can correctly analyse your progress and compare it fairly with the other participants.
* You are allowed to look at the solutions if you are stuck to get further going but if you do that please state that you did in the questionnaire
* Due to the time limit, we also want to encourage you to look at the solutions if you are noticing you are taking too much time for a challenge
* To solve a challenge, you need to find the according flag(s)
* Please note that the flags aren't the only thing you want to find! In some challenges there are hidden hints which you probably need to solve the following challenges. 

  
### Offensive Challenges
#### Challenge 1: Blob Hunt
First, you should take a look at the static PaaS web application 'PurpleWiki'. 

Entry Point: https://thankful-island-0ae319403.5.azurestaticapps.net

Flag Format: PURPLE{...}


#### Challenge 2: FindMe.log
PurpleCloud hosts another web application in the environment. This one is called PurpleBlog and is using an IaaS service model.

Entry Point: http://purpleblog.westeurope.cloudapp.azure.com:8080/

Flag Format: There are two flags in this challenge! The first one is the name of the API endpoint and the second one is the SSH private key.


#### Challenge 3: What is the key to the vault?
You found a way to access the VM! But now what?

Entry Point: VM

Flag Format: Purple{...}


#### Challenge 4: Fetch the Flag (API Edition)
There's a API for employees in the PurpleCloud environment! Bob keeps forgetting the name of the endpoint - do you have a better memory?

Entry Point: API Endpoint

Flag Format: Purple{...}


## Defensive Part
### Introduction
After conducting the penetration test, the CISO will also ask you to review the relevant resources in the Azure cloud and discover the issues identified. Your work will have a direct impact on the security posture of the cloud environment, helping to identify and close vulnerabilities, enforce security policies, enable monitoring for resources and ultimately protect the organisation from potential compromise. Look at the big picture and try to find the vulnerabilities and misconfigurations that will be reported to the CISO so they can take corrective action and implement a security strategy.

**Please read the rules first and the move to the `defensive_handbook.md` in the solutions directory to guide you through this defensive part.**

### Rules
* Once you have completed the offensive part and are aware of the possible vulnerabilities and misconfigurations, you can log in to the Azure portal with your own Azure account.
* Respect the time limit so we can correctly analyse your progress and compare it fairly with the other participants.
* You are allowed to examine all resources provided in the resource group ("purple-cloud-rg") and analyse their configurations.
* You should follow the blue teaming handbook, which will guide you through the defense part and contains information on misconfiguration, corrective defensive measures and monitoring which can be enabled.
* You can perform all corrective measures within the Azure portal.
* You are allowed to skip configuration steps in case something does not work as expected and takes up too much time. You can always come back later the open points and complete them afterwards. In case you are not able to 