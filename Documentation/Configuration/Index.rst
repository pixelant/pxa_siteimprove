.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


.. _configuration:

Configuration Reference
=======================

.. _extension-manager-settings:

Extension Manager settings
--------------------------

Administrators can modify these by going to the Settings module in the TYPO3
backend and clicking on Extension Configuration.

.. image:: _assets/finding-extension-configuration.png
   :alt: Finding the Extension Configuration within the Settings module.

.. image:: _assets/extension-configuration.png
   :alt: The extension configuration options for the Siteimprove extension.

The following configuration options are available for the extension.

.. rst-class:: dl-parameters

Enable debug mode for Siteimprove integration
   :sep:`|` :aspect:`Property name:` debugMode
   :sep:`|` :aspect:`Type:` boolean
   :sep:`|` :aspect:`Default:` false

   Display debug information for the API connection.

Siteimprove token
   :sep:`|` :aspect:`Property name:` token
   :sep:`|` :aspect:`Type:` string
   :sep:`|` :aspect:`Default:` ''

   The Siteimprove API token. This token is necessary to display the correct
   analysis information for your site.

   The token can be generated at https://my2.siteimprove.com/auth/token

.. note::
   In TYPO3 versions prior to version 9, these settings can be found by clicking
   the cogwheel icon in the Extension Manager module.

.. _user-settings:

User settings
-------------

.. rst-class:: bignums-xxl

1. Every user can access their user settings by clicking their username in
   TYPO3's top bar and choosing *User Settings* from the drop-down menu.

2. In the *Edit and Advanced functions* tab of the User settings, a checkbox
   allows each user to individually enable or disable the Siteimprove functions.

.. image:: _assets/user-settings.png
   :alt: Finding Siteimprove in the User Settings

.. rst-class:: dl-parameters

Enable Siteimprove
   :sep:`|` :aspect:`Property name:` use_siteimprove
   :sep:`|` :aspect:`Type:` boolean
   :sep:`|` :aspect:`Default:` false

   A checkbox to toggle Siteimprove on and off

.. _user-tsconfig-settings:

User TSconfig settings
----------------------

You can also turn on and off the Siteimprove functionality for a single user or
a whole user group through User TSconfig settings.

.. image:: _assets/disable-in-tsconfig.png
   :alt: Disabling Siteimprove for a single user.

.. rst-class:: dl-parameters

siteImprove.disable
   :sep:`|` :aspect:`Property name:` siteImprove.disable
   :sep:`|` :aspect:`Type:` boolean
   :sep:`|` :aspect:`Default:` false

   Toggle Siteimprove on and off for a single user or user group.
