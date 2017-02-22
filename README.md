# Getting started with QCubed

[![Build Status](https://travis-ci.org/qcubed/framework.png?branch=master)](https://travis-ci.org/qcubed/framework)
[![Test Coverage](https://codeclimate.com/github/qcubed/framework/badges/coverage.svg)](https://codeclimate.com/github/qcubed/framework/coverage)
[![Issue Count](https://codeclimate.com/github/qcubed/framework/badges/issue_count.svg)](https://codeclimate.com/github/qcubed/framework)

## Releases
**Newest stable release: [version 3.0.6, released on Oct. 25, 2016].

The most recent stable version of version 2 can be found in the v2 branch.

## Community

*Join us on Slack on [qcubed.slack.com](http://qcubed.slack.com). Get your invite [here](https://docs.google.com/forms/d/e/1FAIpQLSe0YTPSbTIFdwyVMS1VqxPK6CnfOlOVJ6Q4OfO-im2IIem5NA/viewform)*. 

## What is QCubed?

QCubed (pronounced 'Q' - cubed) is a PHP Model-View-Controller Rapid Application Development framework with support for PHP5 (5.4 and above) and PHP7. The goal of the framework is to save development time around mundane, repetitive tasks - allowing you to concentrate on things that are useful AND fun. QCubed excels in situations where you have a large database structure that you quickly want to make available to users.

## Stateful architecture

With QCubed, you don't have to deal with POSTs and GETs coming from the browser. QCubed automatically handles that for you and packages the information into object oriented forms and controls. Programming with QCubed feels very much like programming a desktop application. If you are familiar with ASP, it is similar.

## The Code Generator

The Code Generator automatically creates object classes with matching forms and controls based on your database schema. It uses the concept of ORM, [object-relational mapping](http://en.wikipedia.org/wiki/Object-relational_mapping), to practically create your whole model layer for you.

Codegen can take advantage of foreign key relationships and field constraints to generate ready-to-use data models complete with validation routines and powerful CRUD methods, allowing you to manipulate objects instead of constantly issuing SQL queries.

More info as well as examples are available online at <http://examples.qcu.be/>

### Object-oriented querying

Using QQueries allows for simple yet powerful loading of models, all generated ORM classes have Query methods and QQNodes. By using these methods, getting a complex subset of data is pretty straightforward - and can be used on almost any relational database.

## User Interface Library

QCubed uses the concept of a QForm to keep form state between POST transactions. A QForm serves as the controller and can contain QControls which are UI components.

All QControls (including QForm itself) can use a template which is the view layer, completing the MVC structure.

QControls can take advantage of the QForm's FormState to update themselves through Ajax callbacks as easily as synchronous server POSTs. All jQuery UI core widgets are available as QControls.

Some QControls include:
- QDialog
- QTextBox
- QListBox
- QTabs
- QAccordion

The easiest way to learn QCubed is to see the examples tutorial at <http://qcu.be/examples/>

### Plugins

Through its plugin system, QCubed makes it easy to package and deliver enhancements and additions to the core codebase. Plugins for the currently active version of QCubed live in repositories that begin with _plugin_. 

## System Requirements
* A development computer that you can set up so that the browser can write to a directory in your file system.
* v3.0.x, requires PHP 5.4 and above. PHP 7 and HHVM are supported as well.
* All html code is html5 compliant.
* QCubed relies on jQuery for some of its ajax interactions. Also, many of the built-in controls beyond basic html controls require JQuery UI.
* A SQL database engine. MySQL, SqlServer, Postgres, Oracle, PDO, SqlLite, Informix adapters are included. Creating another adapter is not hard if you have a different SQL.

## Installation

The installation procedure is described in detail here: [Installation instructions](https://github.com/qcubed/qcubed/blob/master/INSTALL.md "Installation instructions").

## Upgrading
###2.x -> 3.0
3.0 was a major architectural change from 2.x. You should essentially start over by creating a new project, 
generating your models, using the ModelConnectorEditor to refine what is generated in the connectors(used to be called MetaControls),
and then copying code from your old version to the new version. You will find that many of the things you had to do
by hand are now done in generated code, so it might not take as long as you think. Lets hop :-)

###3.0 -> 3.1
To help with the transition, the new private variable feature is turned off by default. You will
need to turn it on by editing the codegen_settings.xml file and adding a 'privateColumnVars="true"' parameter to the createOptions tag.
See the codegen_settings.xml file in the qcubed/install/project/configuration directory for an example.

This will make all of the protected column variables that are in the Gen class private, so you will not be able to access them
directly from your Model subclasses. For example, if you have a "Name" column in a table, you can do:
 ```php
 $strName = $this->Name;
 ```
 or better yet:
```php
$strName = $this->getName();
```
but not:
```php
$strName = $this->strName;
```

```$this->Name``` routes to ```$this->getName()```

Also, instead of this:
```php
$this->strName = $strName;
```

do:
```php
$this->setName($strName);
```
or
```php
$this->Name = $strName;
```

The benefits of the new method include better performance, reduced OptimisticLocking exceptions, and preventing you from accidentally
accessing a value that was not loaded due to a QSelect clause. Also, QSelect can
now be used to control what is generated when you convert an object to other
formats like JSON.

## Latest commits

A list of the latest changes is available at https://github.com/qcubed/qcubed/commits/master

## Credits

QCubed was branched out of QCodo, a project by Michael Ho. QCubed relies on JQuery and uses jQuery UI libraries for some of its core controls.
