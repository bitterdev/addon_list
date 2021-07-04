**Works with version 5.7 and 8+.**

You are a developer yourself and produce add-ons for concrete5? If this is the case, then the "Add-on list" is the right add-on for you. It allows you to seamlessly integrate your own add-ons and themes from the concrete5 marketplace into your website and offer them for sale or to advertise.

"Add-on list" is a very powerful add-on with a broad range of functions. At the same time, however, it was kept intuitive and simple. Directly during the installation process, you are prompted to enter your user name and the password of your concrete5 account *. Then click on the install-button and it starts: The login data will be saved and the add-ons from your concrete5 account will be synchronized into your local database.

_* Data protection notice: Your login data is stored exclusively and encrypted in your database and transferred to concrete5.org only for authentication using an encrypted HTTPS connection. At the development of this add-on great importance was attached to the highest level of security so that even on the settings page on the Dashboard your password will only be transferred encoded._

After the installation is completed, you will find two new frontend pages in your sitemap:

**1) „My add-ons“**

Here you will find all your add-ons and themes. It is a default page, which in turn contains the "add-on list" block element with the default settings. For an easier start: the overview page is similar to the overview page of concrete5.org. However, you can customize the colors as well as the sorting. The list can be sorted by name, price, number of downloads and by rating. You can also set whether a visitor should be redirected to the concrete5 marketplace by clicking on an entry, or whether an internal page should be used from your sitemap. If you decide upon your own page, you must add the necessary block element "Add-on item" to the target page. This “Add-on-item” was also installed with this add-ons.

**2) Add-on details page**

Here you can get an overview of the selected add-on/theme details which are to be displayed using the "Add-on item" block elements. Here also, care was taken that this site is similar to the details page of the marketplace concrete5, so that an easier operation is possible and a high recognition value is obtained. Like the list item, you can customize the colors of the items and show or hide certain areas. You can also specify whether you want to display an explicit add-on or whether you want to use the (Automatic Recognition) option. If you decide for automatic recognition, the add-ons to be displayed is determined by the GET parameters of the HTTP request header. This method is perfectly compatible with the "add-on list" block element.

The installation routine also adds a dashboard page where you can change your concrete5 logon data at any time. You will find this page as well as a job (which synchronizes your concrete5 add-ons) under "Dashboard > Add-on list".

**The synchronization process**

During synchronization, new add-ons are added, deleted items get deleted, and changes get updated.

_Tip: You should create a CRONJOB for synchronization, so you can automatically synchronize your Marketplace add-ons at certain intervals and your content will always be up to date._

All your add-ons will be transferred during the synchronization process. "Add-on List" supports all enhancements of the modern concrete5 marketplace (version 5.7 or higher) as well as the legacy marketplace (version 5.6 or smaller).

"Add-on list" is multilingual and available in German as well as in English and is 100% responsive. The block elements are displayed clearly on all modern browsers and on all common devices.

This enhancement is aimed exclusively at concrete5 developers, who make add-ons themselves for the concrete5 marketplace. It is the perfect add-ons that allows you to display your own add-ons without much effort on your own website.