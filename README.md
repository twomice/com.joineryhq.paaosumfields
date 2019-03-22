# PAAO Summary Fields

![Screenshot](/images/screenshot.png)

Extends the Summary Fields extension by adding three fields relevant to PAAO:

* Count of Membership Payments $100 or more
* Has five recent consecutive membership payments $100 or more
* Count of of "Member-In-Training" memberships

The extension is licensed under [GPL-3.0](LICENSE.txt).

## Requirements

* PHP v5.4+
* CiviCRM >= 4.7

## Installation (Web UI)

This extension has not yet been published for installation via the web UI.

## Installation (CLI, Zip)

Sysadmins and developers may download the `.zip` file for this extension and
install it with the command-line tool [cv](https://github.com/civicrm/cv).

```bash
cd <extension-dir>
cv dl com.joineryhq.paaosumfields@https://github.com/twomice/com.joineryhq.paaosumfields/archive/master.zip
```

## Installation (CLI, Git)

Sysadmins and developers may clone the [Git](https://en.wikipedia.org/wiki/Git) repo for this extension and
install it with the command-line tool [cv](https://github.com/civicrm/cv).

```bash
git clone https://github.com/twomice/com.joineryhq.paaosumfields.git
cv en paaosumfields
```

## Usage

New fields are added at the bottom of the Summary Fields configuration form, which
is, by default, accessible at Administer > Custom Data and Screens > Summary Fields.

## Issues and support

Please report any issues at https://github.com/twomice/com.joineryhq.paaosumfields/issues
