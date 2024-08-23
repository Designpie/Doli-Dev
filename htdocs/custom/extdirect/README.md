# MOBILID FOR <a href="https://www.dolibarr.org">DOLIBARR ERP CRM</a>

![Mobilid logo](img/object_mobilid.png) 

## Mobilid connector Module

### Features of Mobilid:

Easy management of products - orders - third parties on small screen devices.
Management by automatic barcode reading, no need for search fields.

---

## Version

Mobilid Connector 1.0
Need Dolibarr 4 mini

---

## Install

### From the ZIP file and GUI interface

- If you get the module in a zip file (like when downloading it from the market place [Dolistore](https://www.dolistore.com)), go into
menu ```Home - Setup - Modules - Deploy external module``` and upload the zip file.


Note: If this screen tell you there is no custom directory, check your setup is correct: 

- In your Dolibarr installation directory, edit the ```htdocs/conf/conf.php``` file and check that following lines are not commented:

    ```php
    //$dolibarr_main_url_root_alt ...
    //$dolibarr_main_document_root_alt ...
    ```

- Uncomment them if necessary (delete the leading ```//```) and assign a sensible value according to your Dolibarr installation

    For example :

    - UNIX:
        ```php
        $dolibarr_main_url_root_alt = '/custom';
        $dolibarr_main_document_root_alt = '/var/www/Dolibarr/htdocs/custom';
        ```

    - Windows:
        ```php
        $dolibarr_main_url_root_alt = '/custom';
        $dolibarr_main_document_root_alt = 'C:/My Web Sites/Dolibarr/htdocs/custom';
        ```
        
---

## Configuration

From your browser:

  - Log into Dolibarr as a super-administrator
  - Go to "Setup" -> "Modules"
  - You should now be able to find and enable the module and put ON
  - Click on configuration of the module
  - Connect you devices

---

## Suppression

Put off the module and delete in custom/htdocs

---

## Please check also our other modules:
- [Backorder](https://www.dolistore.com/en/modules/1311-Backorder.html) Backorder management. Keep track of your customer and supplier backorders.
- [Block Outstanding](https://www.dolistore.com/en/modules/1282-Block-Outstanding.html) Block proposals, orders and invoices if outstanding payment amount exceeds limit.

---

## Licenses

### Main code

![GPLv3 logo](img/gplv3.png)

GPLv3 or (at your option) any later version.

See file COPYING for more information.

#### Documentation

All texts and readmes.

![GFDL logo](img/gfdl.png)