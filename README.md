# check_mk zvolsize plugin

This check_mk checks monitor zfs volumes sizes with volsize, used, comp.ratio, snapshot sizes, etc

WIP...

Example output (without Headline) from agent plugin:

```
zfs list -p -t volume -r -o name,volsize,used,available,usedbydataset,usedbysnapshots,compressratio
NAME             VOLSIZE         USED        AVAIL       USEDDS    USEDSNAP  RATIO
zserv/vm1    16106127360  13741113344  66381434880  11687993344  2053120000  1.27x
zserv/vm3    16106127360  14498553856  66381434880  12562055168  1936498688  1.18x
zserv/vm5    26843545600   6466097152  66381434880   5196242944  1269854208  1.26x
...
```

## Install

### OMD Server Install
```
cp checks/zvolsize /omd/sites/$(OMD_SITE)/local/share/check_mk/checks/
cp plugins/zvolsize /omd/sites/$(OMD_SITE)/local/share/check_mk/agents/plugins/
cp templates/check_mk-zvolsize.php /omd/sites/$(OMD_SITE)/local/share/check_mk/pnp-templates/
cp packages/zvolsize /omd/sites/$(OMD_SITE)/var/check_mk/packages/
```  
Or better -> use make target ;-)
```
usage: make [target ...]

pkg:
  pkg......................alias for package
  package..................build check_mk package from installed repo files
  release..................build check_mk package and copy *.mkp file to repo

dev:
  install..................install check_mk plugin, optional OMD_SITE3=hrzg can be overwritten
  purge....................purge check_mk plugin files, optional OMD_SITE3=hrzg can be overwritten

system:
  help.....................show this help
```

### Client Install
```
cp plugins/zvolsize /usr/lib/check_mk_agent/plugins/zvolsize
```
Or download plugin from OMD Server, or rollout plugin with puppet ;-)

### check_mk Packages
#### pack check_mk package (see make target above)

```
#create package meta file (only required if repo dosn't include it yet or the first time)
cmk -vP 
# bundle package file
cmk -vP pack zvolsize
```

#### Other Options for package handling see:
```
cmk -vP
Usage: check_mk [-v] -P|--package COMMAND [ARGS]

Available commands are:
   create NAME      ...  Collect unpackaged files into new package NAME
   pack NAME        ...  Create package file from installed package
   release NAME     ...  Drop installed package NAME, release packaged files
   find             ...  Find and display unpackaged files
   list             ...  List all installed packages
   list NAME        ...  List files of installed package
   list PACK.mkp    ...  List files of uninstalled package file
   show NAME        ...  Show information about installed package
   show PACK.mkp    ...  Show information about uninstalled package file
   install PACK.mkp ...  Install or update package from file PACK.mkp
   remove NAME      ...  Uninstall package NAME

   -v  enables verbose output

Package files are located in /omd/sites/cfa/var/check_mk/packages/.
```


