README
======


Setup
-----

Workbench files should be saved in the Resources/workbench/*.mwb directory inner the bundle that is in the configuration. This is configurable per schema.


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
                params:
                    repositoryNamespace: "Acme\\SomeBundle\\Entity\\Repository"
                    backupExistingFile: true,
                    skipPluralNameChecking: false,
                    enhanceManyToManyDetection: true,
                    bundleNamespace: "",
                    entityNamespace: "",
                    repositoryNamespace: "",
                    useAnnotationPrefix: "ORM\\",
                    useAutomaticRepository: true,
                    indentation: 4,
                    filename: "%entity%.%extension%",
                    quoteIdentifier: false

Execution
=========

To process the files execute the command in the terminal:

	app/console mysqlworkbenchschemaexporter:dump

