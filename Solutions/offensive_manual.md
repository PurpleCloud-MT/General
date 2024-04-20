# PurpleCloud Offensive Manual

## Challenge 1: Blob Hunt 
* Entry Point is the PurpleWiki static web application:

![](https://i.imgur.com/ztYAkZP.png)

* Look at the page source code and realize that the images that are present in some of the wiki entries are included via an external link:
```https://purpleconfidential.blob.core.windows.net/webcontent/databreach_image.png```

![](https://i.imgur.com/1adty2B.png)
* This indicates, that the image is included from a custom Azure Blob Storage resource.
* Try to enumerate the storage by appending ```?restype=container&comp=list``` to the container link:
```https://purpleconfidential.blob.core.windows.net/webcontent?restype=container&comp=list```
* The request returns an XML file as response which provides a listing of the container contents:

![](https://i.imgur.com/02AF02G.png)

* Note that the container is set to anonymous read access which means you can read all the files by visiting the according URL.
* Read ```/webcontent/chat.txt``` to get the following hints:
    * The name of an API endpoint can be found in a log file on a virtual machine (VM) - needed in challenge 4
    * It is possible to access a Key Vault from the VM - needed in challenge 3
* Visit /webcontent/flag/1.txt to get the flag of this challenge.
> PURPLE{PU8L1C_BL08}

## Challenge 2: FindMe.log
* Entry Point is the Purple Blog web application:

![](https://i.imgur.com/aFNpl3P.png)
* Note that the newsletter section includes user input fields:

![](https://i.imgur.com/saZrqEC.png)
* Attempt to cause an error by choosing a birth date that is in the future:

![](https://i.imgur.com/ME9nyzu.png)
* After submitting the form, realize you are getting redirected to the following page:
```/api/process.php?```
* Attempt to exploit a simple OS command injection by appending the following to the URL and notice that it works: 
```process.php?cmd=whoami```
* Execute a ```ls``` command to access the local filesystem of the Azure VM Resource and look at the content of the localadmin home directory: 
```cmd=ls%20../../../../home/localadmin/```
* Note there is a file ```id_rsa``` and a directory ```log```.
* Look at the contents of the ```log``` directory:
```cmd=ls%20../../../../home/localadmin/log```
* Recognize there is a file ```api.log``` in the directory and read it:
```cmd=cat%20../../../../home/localadmin/log/api.log```
* find the name of the API endpoint and an example for an API request in the log file
* Now go back to the home directory and read the ```id_rsa``` file:
```cmd=cat%20../../../../home/localadmin/id_rsa```
* Notice that this is a private key file and save the contents from the page source code (to keep the proper format) as a .pem file.
* After retrieving the name of the API endpoint and the private key file you solved this challenge. 

## Challenge 3: What is the key to the vault?
* Get the IP of the VM (e.g. on windows use ```nslookup.exe purpleblog.westeurope.cloudapp.azure.com```):
```
Name:    purpleblog.westeurope.cloudapp.azure.com
Address:  52.233.176.34
```
* Connect to the VM via SSH as **localadmin** and authenticate using the private key file from the previous challenge (you can either use a commandline or tools like Putty).
* Note that there is an Azure Managed Identity configured allowing you to access a Key Vault from the VM.
* Start with getting the name of the Key Vault by enumerating all Key Vaults in the environment:
```az keyvault list```

![](https://i.imgur.com/jaU7Mks.png)
* After obtaining the name of the Key Vault you can start to enumerate the secrets stored in the resource by using the following command: 
```az keyvault secret list --vault-name purple-secrets```

![](https://i.imgur.com/Dpe44B7.png)

* In the output you can see that in the Key Vault there are two secrets stored, namely **user** and **credential**.
* Now that you have obtained the names of the secrets, you can use them to read the contents with the following commands:
   
```az keyvault secret show --vault-name purple-secrets -n user```

```az keyvault secret show --vault-name purple-secrets -n credential```

![](https://i.imgur.com/c8PrAc7.png)

![](https://i.imgur.com/kAbAikS.png)

* From the output you can retrieve the value of the secrets in plaintext which are:
    * Value of secret **user**: servicepurplecloud@cloudexec.onmicrosoft.com
    * Value of secret **credential**: PurpleCloudDragon123
* You got yourself plaintext credentials for the PurpleCloud service account
* Use these credentials to login to the Azure portal:

![](https://i.imgur.com/g2T1jR4.png)
* Once logged in, navigate to 'All resources'.

![](https://i.imgur.com/r3tk6Vo.png)
* Note that the only resource to which the service account has access to is an Azure Cosmos DB account with the name **purple-db**.

![](https://i.imgur.com/7O3aei5.png)
* Click on the resource to access the properties and navigate to the **Data Explorer**.
* Open the **PurpleDB** database.
* Open the **ServiceData** container.
* Open Items and note there is one item with the /type **flag**.
* After selecting this item to open it, you can find the flag for this challenge in the **value** field.

![](https://i.imgur.com/4rj2yVI.png)

> PURPLE{K3Y_T0_UN1V3R53}


## Challenge 4: Fetch the Flag (API Edition)
* The entry point for this challenge is the API endpoint accessible via https://purplecloud-api.azurewebsites.net/api/user (the name of the endpoint is the first flag vom challenge 2. Additionally, the paramter **id** should have been retrieved from the log file in challenge 2)
* Note that you can't access some of the existing profiles (e.g. request ```/api/user?id=1```):

![](https://i.imgur.com/XWUGiJh.png)
* Some other requests return a valid response (e.g. request ```/api/user?id=3```):

![](https://i.imgur.com/YmNXlyi.png)
* Attempt to request the user with the ID 8 with the following API request:
```https://purplecloud-api.azurewebsites.net/api/user?id=8```

![](https://i.imgur.com/TiU5fj5.png)
* Recognize the addiontal field **supervisor** in the response.
* Now attempt to request the user with the ID 42 (request ```/api/user?id=42```)and note that this user cannot be found: 

![](https://i.imgur.com/r2l9Pgd.png)
* Take one step back and request the user with the ID 8 (request ```/api/user?id=8```) again.
* Now note that there is an IDOR vulnerability which allows you to switch to any profile by editing the ID in the URL
* Attempt to access the profile of the user with the ID 42 using this approach and find the flag.

![](https://i.imgur.com/LiljMTg.png)

> PURPLE{1NS3CUR3_4P1}
