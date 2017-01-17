# check_mk zvolsize plugin

This check_mk checks monitor zfs volumes sizes with volsize, used, comp.ratio, snapshot sizes, etc

WIP...

Example output (without Headline) from agent plugin:

````
zfs list -p -t volume -r -o name,volsize,used,available,usedbydataset,usedbysnapshots,compressratio
NAME             VOLSIZE         USED        AVAIL       USEDDS    USEDSNAP  RATIO
zserv/vm1    16106127360  13741113344  66381434880  11687993344  2053120000  1.27x
zserv/vm3    16106127360  14498553856  66381434880  12562055168  1936498688  1.18x
zserv/vm5    26843545600   6466097152  66381434880   5196242944  1269854208  1.26x
...
```

## Install

### on OMD Server
````
# check is required
cp checks/zvolsize  /omd/sites/<SITE_NAME>/share/check_mk/checks/zvolsize

# template is optional, when not present default.php from png4nagios will be used
cp templates/check_mk-zvolsize.php /omd/sites/<SITE_NAME>/share/check_mk/pnp-templates/

# this is optional to be able to download plugin. It must be present on clients not on server
cp plugins/zvolsize /omd/sites/<SITE_NAME>/share/check_mk/agents/plugins/zvolsize




```  

### on client
```
cp plugins/zvolsize /usr/lib/check_mk_agent/plugins/zvolsize
```

### TODO: pack OMD pkg for easy install

