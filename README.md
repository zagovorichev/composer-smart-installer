# Composer installs different packages to specified folders

### How to use

### Environment variables
>Notice: none of these variables are required, composer will work by default, without variables.
- `GIT_MODULES` list of modules to install into the `MODULES_DIR_PATH` directory. Example: `gir_repo1 git_repo2`
- `VENDOR_DIR_PATH` default for __composer__ is `vendor` directory in the root of project, can be changed.
- `MODULES_DIR_PATH` path for the repositories from the `GIT_MODULES` list. Default value is root directory (near 
  the `vendor` directory)
- `MODULES_NAME_MAP` Mapped names for the packages, to use different names for folders. Example: `git_repo1:dir1 
  git_repo3:dirname`. Default is the name of the package.
  