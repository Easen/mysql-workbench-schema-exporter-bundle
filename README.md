README
======


Setup
-----

Workbench files should be saved in the resources/workbench/*.mwb directory. This is configurable per schema.


Configuration
=============

Single schema
-------------

`schema_name` here refers to name of the Workbench file

 mysql_workbench_schema_exporter:
     schema:
         schema_name:
             bundle: YourBundle


Multiple schemas
----------------

`schema_name` here refers to name of the Workbench file

 mysql_workbench_schema_exporter:
     schema:
         schema1_name:
             bundle: YourBundle
         schema2_name:
             bundle: YourBundle
         schema3_name:
             bundle: YourBundle

