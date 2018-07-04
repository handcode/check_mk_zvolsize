# Makefile for check_mk zvolsize plugin

# ENV OMD_SITE with valid omd site name must be set!
# export OMD_SITE=cfa

.PHONY: install purge package pkg release

# --------------------
# Defines
# --------------------
# check user and decided wether we should execute commands with sudo prefix?
user = $(shell whoami)
PWD ?= $(shell pwd)


guard-%:
	@ if [ -z '${${*}}' ]; then echo 'Environment variable $* not set!' && exit 1; fi

ifneq ($(user),root)
$(error You must be root to run this.)
endif

CMK_CHECK_DIR = /omd/sites/${OMD_SITE}/local/share/check_mk/checks/
CMK_PLUGIN_DIR = /omd/sites/${OMD_SITE}/local/share/check_mk/agents/plugins/
CMK_TMPL_DIR = /omd/sites/${OMD_SITE}/local/share/check_mk/pnp-templates/
CMK_PKG_DIR = /omd/sites/${OMD_SITE}/var/check_mk/packages/
CMK_REL_TMP_DIR = /omd/sites/${OMD_SITE}/release-tmp
CMK_WATO_PLUGIN_DIR = /omd/sites/${OMD_SITE}/local/share/check_mk/web/plugins/wato/

# --------------------
# Targets
# --------------------
default: help

install: guard-OMD_SITE ##@dev install check_mk plugin, optional OMD_SITE3=hrzg can be overwritten
	test -d $(CMK_CHECK_DIR) && cp checks/zvolsize  $(CMK_CHECK_DIR)
	test -d $(CMK_PLUGIN_DIR) && cp plugins/zvolsize  $(CMK_PLUGIN_DIR)
	test -d $(CMK_TMPL_DIR) && cp templates/check_mk-zvolsize.php  $(CMK_TMPL_DIR)
	test -d $(CMK_PKG_DIR) && cp packages/zvolsize  $(CMK_PKG_DIR)
	test -d $(CMK_WATO_PLUGIN_DIR) && cp web/plugins/wato/check_parameters_zvolsize.py  $(CMK_WATO_PLUGIN_DIR)

purge:  guard-OMD_SITE ##@dev purge check_mk plugin files, optional OMD_SITE3=hrzg can be overwritten
	test -f $(CMK_CHECK_DIR)/zvolsize && rm $(CMK_CHECK_DIR)/zvolsize
	test -f $(CMK_PLUGIN_DIR)/zvolsize && rm $(CMK_PLUGIN_DIR)/zvolsize
	test -f $(CMK_TMPL_DIR)/check_mk-zvolsize.php && rm $(CMK_TMPL_DIR)/check_mk-zvolsize.php
	test -f $(CMK_PKG_DIR)/zvolsize && rm $(CMK_PKG_DIR)/zvolsize
	test -f $(CMK_WATO_PLUGIN_DIR)/check_parameters_zvolsize.py && rm $(CMK_WATO_PLUGIN_DIR)/check_parameters_zvolsize.py

pkg: ##@pkg alias for package
pkg: package

package: ##@pkg build check_mk package from installed repo files
package: guard-OMD_SITE install
	echo "build check_mk package from installed repo files"
	su - ${OMD_SITE} -c 'mkdir -p release-tmp && cd release-tmp && cmk -vP pack zvolsize && pwd'

release: ##@pkg build check_mk package and copy *.mkp file to repo
release: guard-OMD_SITE package
	echo "move created package file to repo"
	test -d $(CMK_REL_TMP_DIR) && test -f $(CMK_REL_TMP_DIR)/zvolsize-*.mkp && chown root:root $(CMK_REL_TMP_DIR)/zvolsize-*.mkp && mv $(CMK_REL_TMP_DIR)/zvolsize-*.mkp ./releases/ && rmdir $(CMK_REL_TMP_DIR)
	echo "DONE, if you wish, please commit created mkp file to repo"
	ls -latr ./releases/*.mkp

# --------------------
# Thanks to dmstr:
# --------------------
# Help based on https://gist.github.com/prwhite/8168133 thanks to @nowox and @prwhite
# And add help text after each target name starting with '\#\#'
# A category can be added with @category
# -----------------------------------
HELP_FUN = \
                %help; \
                while(<>) { push @{$$help{$$2 // 'options'}}, [$$1, $$3] if /^([\w-]+)\s*:.*\#\#(?:@([\w-]+))?\s(.*)$$/ }; \
                print "\nusage: make [target ...]\n\n"; \
        for (keys %help) { \
                print "$$_:\n"; \
                for (@{$$help{$$_}}) { \
                        $$sep = "." x (25 - length $$_->[0]); \
                        print "  $$_->[0]$$sep$$_->[1]\n"; \
                } \
                print "\n"; }

help:   ##@system show this help
	@perl -e '$(HELP_FUN)' $(MAKEFILE_LIST)

