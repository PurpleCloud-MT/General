# Purple Cloud - Blue Teaming Handbook
## Introduction
After conducting the penetration test, the CISO will also ask you to review the relevant resources in the Azure cloud and discover the issues identified. Your work will have a direct impact on the security posture of the cloud environment, helping to identify and close vulnerabilities, enforce security policies, enable monitoring for resources and ultimately protect the organisation from potential compromise. Look at the big picture and try to find the vulnerabilities and misconfigurations that will be reported to the CISO so they can take corrective action and implement a security strategy.
### Overview
This handbook consists of four manuals highlighting the misconfigurations and threats associated with the cloud security challenges within PurpleCloud and show the mitigation/remediation steps from a defensive perspective. A participant should follow this handbook after taking the offensive part of the project to understand and learn where the flaws are in the Azure resources exposed that allow an attacker to exploit them.

It reinforces learning by providing hands-on Blue Teaming experiences directly in Azure and following the steps along with configuration facts. This approach provides a comprehensive understanding of each vulnerability from both an attack and defense perspective.

### Objectives
* Gain a deeper understanding of cloud security from a blue team perspective
* Learn about security architecture of Azure resources and common vulnerabilities/misconfigurations that can arise in such environments
* Learn to implement and verify security measures directly within the Azure portal ensuring a practical and hands-on approach to cloud security
* Understand security best practices and compliance requirements relevant to Azure resources

### Azure Access
* You received your dedicated access via your Azure account to the "PurpleCloud" tenant.
* After login in the [Azure portal](https://portal.azure.com) you may need to switch directory if you do not see "PurpleCloud" directly in the top right-hand corner. To do this, click on your account name in the top right-hand corner and select 'Switch directory', where you can select the "PurpleCloud" tenant:

![](https://i.imgur.com/mQwQRmb.png)
![](https://i.imgur.com/XsLgrfd.png)
* To find the relevant Azure resources for the challenges, navigate to the resouce groups by searching in the Azure search bar and selecting them

![](https://i.imgur.com/3mU9uu8.png)
* The only resource group available is the "purplecloud-rg" which can be selected
![](https://i.imgur.com/Nzt8E5p.png)
* In the resource group overview the Azure resources deployed in this group are directly presented in the main section
![](https://i.imgur.com/n2ukEtw.png)

## Challenge 1: "Blob Hunt"
### Resources
* *Name:* PurpleWiki
*Type:* Static Web App

* *Name:* purpleconfidential
*Type:* Storage Account

### Scenario Description
The challenge starts with the static web application that has the only purpose to display static pages such as Wiki entries. In one of the articles there is an image which is hosted in a blob storage container according to the source URL.

![](https://i.imgur.com/u062eUY.png)

Due to the configuration the container is accessible and can also be enumerated: 
* URL: `https://purpleconfidential.blob.core.windows.net/webcontent`
* Enumerate the container: `?restype=container&comp=list`

![](https://i.imgur.com/WWUwTiQ.png)

### Defensive Path
#### > REVIEW & IDENTITY <
* Lets start from the resource group overview (mentioned under section "Azure Access")
* The Azure Resources in scope for Challenge 1 are:

![](https://i.imgur.com/tVG3DMr.png)
##### Static Web App
* It is pretty basic static web app which is deployed from a GitHub repository via GitHub Actions

![](https://i.imgur.com/AOsA5KU.png)
* The following parts are interesting to check if there are any misconfigurations for this type of resource:

![](https://i.imgur.com/96nHlp6.png)
* *Configuration:* Password Protection could be enabled in case it is a private web application, not necessary for the PurpleWiki.
* *Application Insights:* Monitoring is not enabled for this web app. This should be enabled if a web app with dynamic functionalities is deployed, not relevant for this static web app.
* *Identity:* No identity is configured, hence no security risk. 
* *Locks:* The resource is not locked which means anyone with permissions can manipulate the configuration of the web app. 
**You can add a "Read-Only" lock on this resource preventing untrusted changes:**

![](https://i.imgur.com/SrZ2NBP.png)

##### Storage Account
* Directly in the Storage Account overview we can see the properties of the resource in one view and identify misconfigurations:

![](https://i.imgur.com/LBg2JKd.png)
* Under `Encryption` we can see that the PurpleCloud organization is using the Microsoft-managed keys for encryption which should be switched to Customer-managed keys if possible to ensure full confidentiality of restricted data:

![](https://i.imgur.com/p3v7866.png)
* The container `webcontent` is enumerateable since the anonymous access level is set to container:

![](https://i.imgur.com/MtllOo6.png)

**Misconfigurations:**
1. The blob storage is publicly accessible
2. It is allowing access from all networks
3. Soft Delete is disabled
4. Using Microsoft-managed encryption key
5. Container "webcontent" can be enumerated

----
#### > MITIGATE <
* First we can disable anonymous blob access by heading to `Settings` --> `Configuration` and switching it to disabled. This will not make the content publicly available, for this "Shared Access Signatures" combined with a managed identity, please refer to [1].
Tip: The configurations can also be accessed via the storage account overview.

![](https://i.imgur.com/5OnOmaK.png)

* Under `Networking` we may limit the access to a specific virtual network or IP addresses such as our client IP. 

![](https://i.imgur.com/Vr4UgOh.png)
![](https://i.imgur.com/c09xTkp.png)

* With soft-delete enabled data can be restored for a specific period of time, in case an error happens. `Data Management` --> `Data protection` --> `Recovery` is where both blob and container soft delete can be enabled.

![](https://i.imgur.com/NpBQSXq.png)
* To demonstrate the soft delete behavior you can head over the `Data storage` --> `Container` and toggle the following switch on the right side to see also deleted containers. A hidden container will appear which was previously deleted while soft delete was enabled.

![](https://i.imgur.com/oxKctU3.png)
 
* To use customer managed keys we can use the already created `enc-key` for this demonstration in the Azure Key Vault and select to use it for the encryption. A new key could be also directly created in the Key Vault if necessary.

![](https://i.imgur.com/frGZC9k.png)

* To block the enumeration of the container we can change the access level from `Container` to `Blob`

![](https://i.imgur.com/7B9i3Ep.png)
* In case public access is already disabled this is automatically done:

![](https://i.imgur.com/SzKfB2M.png)
* If this is not the case we can do it manually:

![](https://i.imgur.com/Yztpmk6.png)

* After all steps you may retry the enumeration of the files in the container. This should not be possible anymore.

---
#### > MONITOR <
* To enable monitoring of the Storage Account which can be used with Microsoft Sentinel (cloud-native SIEM + SOAR solution)
* We can navigate to the "Monitoring" section and then select "Diagnostic settings". Here we can select "blob" for the storage account and proceed with the creation of a diagnostic setting.

![](https://i.imgur.com/rT5qLcW.png)
![](https://i.imgur.com/VOQ4eVd.png)
* We specify a diagnostic setting name, e.g. "blob-audit" and select the the audit logs and send them to the Log Analytics Workspace `purple-we-law` which is stores the data used by Microsof Sentinel, and save this setting.

![](https://i.imgur.com/TjVXksA.png)
* We can now open Microsoft Sentinel by searching for it and selecting the previously specified log analytics workspace.

![](https://i.imgur.com/jnoFBdQ.png)
![](https://i.imgur.com/5Ppc5qF.png)
* In Sentinel we navigate to `Logs` where we can query the `StorageBlobLogs` table. 
**Hint: It can take about 5 minutes that logs are ingested in the table. Therefore you can try to access a blob file and come back later to check the logs.**

![](https://i.imgur.com/0vADqDs.png)
![](https://i.imgur.com/4ORcZhE.png)

## Challenge 2: "FindMe.log"
### Resources
* *Name:* purpleblog
*Type:* Virtual Machine

* *Name:* '02a98d68-d38d-4cfe-a76b-69a8ff2eec65' as the object ID for the virtual machine
*Type:* Managed Identity

### Scenario Overview
This challenge begins with the PurpleBlog web application, which is publicly accessible at: `http://purpleblog.westeurope.cloudapp.azure.com:8080/`

![](https://i.imgur.com/twRputI.png)

It includes a command injection vulnerability which can be used by an attacker to execute commands on the virtual machine hosting the application. The application is provided with an **NGINX server** running on the virtual machine. 

### Defensive Path
#### REVIEW & IDENTIFY
* Lets start from the resource group overview again (mentioned under section "Azure Access")
* The Azure Resources in scope for Challenge 2 are:

![](https://i.imgur.com/OHUBlTH.png)
* The main component is the virtual machine `purpleblog`, for which the other resources such as public IP address, virtual network, network security group (NSG) as well as the network interface card and OS disk are provisioned.
* Lets take a look at virtual machine and investigate how it is configured

![](https://i.imgur.com/1r2Su6p.png)
* It is evident that is an Ubuntu 20.04. machine that is assigned a public IP address with a DNS name. The virtual machine is located in a virtual network called `purpleblog-vnet`, for which we can check the NSG rules as well as for the machine itself
* Taking a look at the `Network Settings` we can find that SSH on the default port 22 and port 8080 for the web application are opened.

![](https://i.imgur.com/uIYcBbK.png)
* We can see that SSH has warning sign which when we click on the rule tells us that the best practice is to not expose SSH to the Internet in productive environments to prevent compromise

![](https://i.imgur.com/T4ZpzCb.png)
* Going into the `Disks` setting we can check out if the disk is encrypted or not. For this let us select `Additional settings`

![](https://i.imgur.com/EoDGNjq.png)
* Here we can clearly see that the virtual machine

![](https://i.imgur.com/VzYcCGU.png)
* Next, there is the `Identity` under the Security section which tells us that a managed identity exists and has permissions to perform actions in Azure. This is not a misconfiguration but rather a best-practice approach allowing access of the VM to other Azure resources.

![](https://i.imgur.com/CMMjGJJ.png)
* It shows that can access the "purple-secrets" Azure Key Vault and read the values stored in the key vault

![](https://i.imgur.com/ALUFAuK.png)
* As we can see the virtual machine is not being monitored at the moment

![](https://i.imgur.com/WV5cBbr.png)
* Last but not least, we can view the vulnerable part of the code in the GitHub repository under `Defense_Resouces` in the file `process.php`

![](https://i.imgur.com/8WdouPK.png)
* Here we can observe that PHP is used and expects a GET parameter `cmd`, which executes passed arguments on the virtual machine.

**Misconfigurations:**
1. SSH access via public IP address
2. No disk encryption **(will not to be mitigated as part of this challenge)**
3. No monitoring of NGINX server
4. Poor software development of web application leading to OS command injection

#### MITIGATE
* We can deny the SSH access as the first mitigation step. For this we head over to the `Network Settings` and change the already existing inbound NSG rule

![](https://i.imgur.com/LINfvuE.png)
* By selecting the SSH rule we can change the action to `DENY` and save it.

![](https://i.imgur.com/JFCsQxk.png)
* To have some monitoring of the server we will discuss the approach in the next section *"MONITOR"*
* For poor software development we can note this down a let the CISO know about the vulnerable piece of code which needs to be removed. It makes sense to start with some CI/CD best-pratices to do some vulnerability checks/scans before the code is deployed into production. 

#### MONITOR
* To enable monitoring lets go directly into Microsoft Sentinel where for NGINX there is a data connector we can use to monitor the logs of the NGINX server running on the virtual machine

![](https://i.imgur.com/CRtnSUm.png)
![](https://i.imgur.com/3lyGJMG.png)
* We follow along with the configurations mentioned, first installing the Azure Linux Monitor agent

![](https://i.imgur.com/7S4M0YO.png)
![](https://i.imgur.com/n7BO2nu.png)
* After it is connected we can move into configuring the log collection by creating a NGINX custom table

![](https://i.imgur.com/poEPjrF.png)
![](https://i.imgur.com/B1xX5hp.png)
* An table was already created beforehand and will be automatically populated with logs when there are new NGINX logs on the virtual machine. For reference we can check if the table exists

![](https://i.imgur.com/fAXodXD.png)
* In case it would not be created yet we could follow this reference: [2]. For this we would need to upload a "example-access.log" which can be also found in the Github Repository under "defense_resouces" folder and proceed with further settings. This example file allows to correctly delimit individual events.
* After some time, we can check the `Logs` in Sentinel and query our table to see the recent log events.

![](https://i.imgur.com/4Az6LlJ.png)

## Challenge 3: “What is the key to the vault?”
### Resources
* *Name:* purple-secrets
*Type:* Azure Key Vault

* *Name:* purple-db
*Type:* Azure Cosmos DB account

### Scenario Overview
Following up from Challenge 2, the managed identity of the virtual machine provides read access to the Azure Key Vault where two secrets are stored containing the password and the username which is also evident from the log file located on the virtual machine. With these two pieces of information, the attacker can log in as the Purple Cloud service account user. This user has access to the Cosmos DB and can access the the data. If that is not enough the user has a pretty weak password which could be also brute forced.

### Defensive Path
#### REVIEW & IDENTIFY
* Lets start from the resource group overview again (mentioned under section "Azure Access")
* The Azure Resources in scope for Challenge 3 are:

![](https://i.imgur.com/AxA88oW.png)

##### Azure Key Vault
* The Azure Key Vault called `purple-secrets` can store various secret material such as keys, secrets and certificates. Therefore it is important that it is configured the best way as possible to minimize risk.
* Interesting settings we can check are:

![](https://i.imgur.com/dNqniXr.png)
    * Access Control (IAM)
    * Access configuration
    * Networking
    * Monitoring
* We can see that the managed identity of the `purple-blog` virtual machine has access to the key vault secrets and can read the key vault itself. This is why on the offensive part we were able to access the key vault secrets.

![](https://i.imgur.com/lfznSHh.png)
* This is a legitimate approach and **managed identities should be used where possible**. For this challenge in particular, it can unfortunately be misused as an attack vector due to previous vulnerabilities in the virtual machine itself.
* For access configuration the recommended approach by Azure is enabled with role-based access control. Nothing to mitigate here.

![](https://i.imgur.com/Tl9Nvvb.png)
* In the `Networking` configuration we can identify that the Key Vault is publicly accessible which should be mitigated.

![](https://i.imgur.com/pGK8wH0.png)
* Monitoring for this resource is not enable but can be done via the diagnostics settings in the next sections.

![](https://i.imgur.com/an9rRRq.png)

##### Azure Cosmos DB
* For the database we can check similar things as for the Key Vault. Access control (IAM) and Networking can be interesting in this context.

![](https://i.imgur.com/xrDyEKH.png)
* We can see that the service account has read permissions for the DB account. This means that it is restricted to read-only access and is only available to a specific user which explains the attack path.

![](https://i.imgur.com/Yl4T2Pu.png)
* Similar to the key vault, the Cosmos DB is also publicly accessible, which is not necessary in this case as only an internal user has read access. 

![](https://i.imgur.com/ESSqDGX.png)
* Also, under the the `Monitoring` there are no measure in place, i.e. no diagnostic settings available at the moment.

**Misconfigurations:**
1. Unnecessary full public access for both resources
2. Service Account has not yet MFA enforced, therefore it was easy for the attacker to access Azure with the email and password.

#### MITIGATE
* In order to limit the access we can specify specific IP addresses which can access the key vault. For this example we can use the public IP address of the virtual machine or our own client IP.

![](https://i.imgur.com/cTvdb14.png)
* When only adding the IP of the VM and applying the changes we are not able to view the key vault anymore

![](https://i.imgur.com/npw0DPP.png)
* The access can also be limited for the Cosmos DB

![](https://i.imgur.com/Xjhbvff.png)
* Here we can add our own client IP and/or allow access from Azure Portal.
* Ultimately, enforcing MFA as a mandatory requirement for all accounts in the organisation would stop part of the attack when the intruder logs in as a service account user.

#### MONITOR
* Both resources can be monitored, hence audit logs collected and analyzed. For this we can use the same approach as for previous challenges where we added diagnostic settings to forward to logs to the log analytics workspace / Sentinel.

**Azure Key Vault**
![](https://i.imgur.com/NfH97Tz.png)
![](https://i.imgur.com/DDGeqg3.png)
**Azure Cosmos DB**
![](https://i.imgur.com/IH9etzG.png)
![](https://i.imgur.com/HORnF22.png)

* After some time we can see in Sentinel that the logs are correctly ingested and can be further analyzed.
![](https://i.imgur.com/h5FmPRl.png)
![](https://i.imgur.com/sLEOdyA.png)
![](https://i.imgur.com/UQEnV6N.png)


## Challenge 4: “Fetch the Flag (API Edition)”
### Resources
* *Name:* purplecloud-api
*Type:* Function App

### Scenario Overview
An API is the main component of this challenge. It handles requests to show user details and is misconfigured so that some users can be leaked by entering the correct ID as an URL parameter. The vulnerability involved here is a type of Insecure direct object reference (IDOR). 

### Defensive Path
#### REVIEW & IDENTIFY
* Lets start from the resource group overview again (mentioned under section "Azure Access")
* The Azure Resource in scope for Challenge 4 are:

![](https://i.imgur.com/kyHErG1.png)
* Looking at the different configurations there are not specific issues which make the function app more vulnerable. Still, we can take a look at the code itself by checking the `function_app.py` in the `App files`.

![](https://i.imgur.com/Il6cBvK.png)
* Most of the code is not yet ready for production and has been deployed without a security check or code review. 

**Misconfigurations:**
1. Poor software development of API leading to information leakage / IDOR

#### MITIGATE
* The only mitigation is to establish a secure software development cycle that includes code review. CI/CD can generally be a good start here.
* To fix the bug as quickly as possible, we remove the if condition in the code and save the file, which the API automatically redeploys.

![](https://i.imgur.com/mkKRH7P.png)
* By retesting we can see that any id leads to unauthorized access at the moment

![](https://i.imgur.com/jv7uqmv.png)

#### MONITOR
* Monitoring of this resource may have helped to detect this issue earlier and act faster. Therefore, we can create a diagnostic setting which will send all function app logs to the Log Analytics Workspace / Sentinel.

![](https://i.imgur.com/wksJJ4T.png)
![](https://i.imgur.com/u3Bo2TS.png)
* We can again verify the logs directly in Sentinel after some time and test cases on the API

![](https://i.imgur.com/6r8IsQr.png)

## References
[1] https://learn.microsoft.com/en-us/azure/storage/common/storage-sas-overview
[2] https://learn.microsoft.com/en-us/azure/sentinel/data-connectors/nginx-http-server