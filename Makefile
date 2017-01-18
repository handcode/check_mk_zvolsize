# Makefile for docker-compose stacks

.PHONY: install 

# --------------------
# Defines
# --------------------
# check user and decided wether we should execute commands with sudo prefix?
user = $(shell whoami)
PWD ?= $(shell pwd)
OMD_SITE = cfa

ifneq ($(user),root)
$(error You must be root to run this.)
endif

CMK_CHECK_DIR = /omd/sites/$(OMD_SITE)/local/share/check_mk/checks/
CMK_PLUGIN_DIR = /omd/sites/$(OMD_SITE)/local/share/check_mk/agents/plugins/
CMK_TMPL_DIR = /omd/sites/$(OMD_SITE)/local/share/check_mk/pnp-templates/
CMK_PKG_DIR = /omd/sites/$(OMD_SITE)/var/check_mk/packages/

# --------------------
# Targets
# --------------------
default: help

install:   ##@test install check_mk plugin, optional OMD_SITE3=hrzg can be overwritten
	test -d $(CMK_CHECK_DIR) && cp checks/zvolsize  $(CMK_CHECK_DIR)
	test -d $(CMK_PLUGIN_DIR) && cp plugins/zvolsize  $(CMK_PLUGIN_DIR)
	test -d $(CMK_TMPL_DIR) && cp templates/check_mk-zvolsize.php  $(CMK_TMPL_DIR)
	test -d $(CMK_PKG_DIR) && cp packages/zvolsize  $(CMK_PKG_DIR)

purge:   ##@test purge check_mk plugin files, optional OMD_SITE3=hrzg can be overwritten
	test -f $(CMK_CHECK_DIR)/zvolsize && rm $(CMK_CHECK_DIR)/zvolsize
	test -f $(CMK_PLUGIN_DIR)/zvolsize && rm $(CMK_PLUGIN_DIR)/zvolsize
	test -f $(CMK_TMPL_DIR)/check_mk-zvolsize.php && rm $(CMK_TMPL_DIR)/check_mk-zvolsize.php
	test -f $(CMK_PKG_DIR)/zvolsize && rm $(CMK_PKG_DIR)/zvolsize

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

