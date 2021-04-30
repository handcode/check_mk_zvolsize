#!/usr/bin/env python
# -*- encoding: utf-8; python-indent-offset: 4 -*-

# Author: Jens Giessmann jg@handcode.de

register_check_parameters(
    subgroup_storage,
    "zvolsize",
    _("ZFS Volume sizes: level options"),
    Dictionary(
        elements = [
            ('levels', Tuple(
                title = _('Levels for warning and critical states'),
                help = _("Levels for warning and critical as decimal"),
                elements = [
                    Float(title = _("Warning used by data and snapshots"), unit = _("decimal"), default_value = 1.5),
                    Float(title = _("Critical used by data and snapshots"), unit = _("decimal"), default_value = 2.0),
                    Float(title = _("Warning used by snapshots"), unit = _("decimal"), default_value = 0.5),
                    Float(title = _("Critical used by snapshots"), unit = _("decimal"), default_value = 1.0),
                ])),
        ],
    ),
    TextAscii(
        title = _("Name of service"),
        allow_empty = False,
    ),
    'dict',
)
