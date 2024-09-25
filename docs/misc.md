# Setup the terraform backed in the dev environment

The terraform backend is used to store the state of the infrastructure. This is useful when
you work on the infrastructure from you development machine and you want to share the state with the 
github actions.

To setup the terraform backend, you need to create a storage account in Azure and a container in the storage account.
This is done in this project using a **GitHub Action** workflow named: `0.terraform.backend.yml`.

The workflow is triggered only **manually**.

If you want to use the same backend in the *codespaces* or local machine, you need to setup the backend in the code spaces as well.
For this purpose there was defined in the `terraform` folder a `terraform.backend-config.tfvars` file that contains the configuration of the backend.
To use this configuration you need to run the following command in the `terraform` folder:

```bash
terraform init -backend-config=terraform.backend-config.tfvars
```

# login with the terraform service principal

I created in the terraform folder a `terraform.login.bash` script that will login with the service principal created for provisioning the infrastructure.
To use this script you need to run the following command in the `terraform` folder:

```bash 
source ./terraform.login.bash
```

Note: When creating the service principal for terraform, you need to give it the `Owner` role on the resource group where the infrastructure will be created.
