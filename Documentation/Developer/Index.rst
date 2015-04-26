.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


.. _developer:

Developer Corner
================

Mixing layout and programmation is not a good idea ?
----------------------------------------------------

| Bootstrap offer a good level of abstraction in the way layout are made. Handling design excluding device specific targets ensure stability over time. At some extend, it is safe to base programmation over the grid system.
|
| There is one major drawnback : when it comes to handle fine grained layout options (eg:content frames) design impacts directly on programmation. This is where the lib.registerFrameSize customisation enter in the game. Again, the bootstrap_package allow only a limited set of content frames (allready handled by lib.registerFrameSize), so layout won't influence too mutch in programmation.
|
| On the other hand, being able to provide the right image size on any device and layout including deeply nested one is a big advantage, and there is a price to pay for it : pre-compute the layout width.


.. _developer-api:


API
---

Initialisation of number of columns
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

lib.dynamicContent is a part of the bootstrap package used in fluidtemplates used to select dynamically the rendered colPos in template via variable.
This part initialise number of columns on templates where columns are not set by variables (eg: on 12 columns).

::

  #####################################
  ## Initialise number of columns
  #####################################

  lib.dynamicContent.5 = LOAD_REGISTER
  lib.dynamicContent.5 {

    columns_xs {
        cObject = TEXT
        cObject.value = {$plugin.lef_responsive_images.columns}
    }
    columns_sm < .columns_xs
    columns_md < .columns_xs
    columns_lg < .columns_xs
  }


Typical gridelement column container columns setup
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

Using registers to propagate flexforms setting and nested layouts width.

::

  #####################################
  ## 1 column
  #####################################

  lef_bootstrap_1col {

    columns.0 {

        renderObj = COA
        renderObj {

            # 4 registers to propagate smallest layouts columns to larger ones

            10 = LOAD_REGISTER
            10 {
                columns_xs {
                    cObject = TEXT
                    cObject{

                        # Default number of columns : 12
                        value = {$plugin.lef_responsive_images.columns}

                        # Number of columns for this column on xs breakpoint
                        override.field = parentgrid_flexform_width_column_xs_1

                        # Only if this breakpoint is active
                        override.if.isTrue.field = parentgrid_flexform_xsmall
                    }
                }
            }
            11 = LOAD_REGISTER
            11 {
                columns_sm{
                    cObject = TEXT
                    cObject {
                        # propagate default number of columns across breakpoints
                        data = register:columns_xs
                        override.field = parentgrid_flexform_width_column_sm_1
                        override.if.isTrue.field = parentgrid_flexform_small
                    }
                }
            }
            12 = LOAD_REGISTER
            12 {
                columns_md{
                    cObject = TEXT
                    cObject {
                        data = register:columns_sm
                        override.field = parentgrid_flexform_width_column_md_1
                        override.if.isTrue.field = parentgrid_flexform_medium
                    }
                }
            }
            13 = LOAD_REGISTER
            13 {
                columns_lg{
                    cObject = TEXT
                    cObject {
                        data = register:columns_md
                        override.field = parentgrid_flexform_width_column_lg_1
                        override.if.isTrue.field = parentgrid_flexform_large
                    }
                }
            }

            # Now compute real layout width for each breakpoint
            20 = LOAD_REGISTER
            20 {
                width_xxs {
                    cObject = COA
                    cObject{
                        20 = TEXT
                        20.data =  register:width_xxs
                        30 = TEXT
                        30.value = /{$plugin.lef_responsive_images.columns}*
                        40 = TEXT
                        40.data = register:columns_xs
                    }
                    prioriCalc=1
                }
                width_xs < .width_xxs
                width_xs.cObject {
                    20.data = register:width_xs
                    40.data = register:columns_xs
                }
                width_sm < .width_xxs
                width_sm.cObject{
                    20.data = register:width_sm
                    40.data = register:columns_sm
                }
                width_md < .width_xxs
                width_md.cObject{
                    20.data = register:width_md
                    40.data = register:columns_md
                }
                width_lg < .width_xxs
                width_lg.cObject{
                    20.data = register:width_lg
                    40.data = register:columns_lg
                }
            }

            # Render column content
            30 = < tt_content

            # 10-13 Restore columns register
            40 = RESTORE_REGISTER
            41 = RESTORE_REGISTER
            42 = RESTORE_REGISTER
            43 = RESTORE_REGISTER

            # 20 Restore width register
            50 = RESTORE_REGISTER

    }



    #####################################
    ## 2 columns
    #####################################

    lef_bootstrap_2col < .lef_bootstrap_1col
    lef_bootstrap_2col {
        columns.1 < .columns.0
        columns.1 {

            renderObj{

                # Overrides with flexform fields value for column 2
                10{
                    columns_xs.cObject.value =  {$plugin.lef_responsive_images.columns}
                    columns_xs.cObject.override.field =  parentgrid_flexform_width_column_xs_2
                }
                11 {
                    columns_sm.cObject.data = register:columns_xs
                    columns_sm.cObject.override.field = parentgrid_flexform_width_column_sm_2
                }
                12 {
                    columns_md.cObject.data = register:columns_sm
                    columns_md.cObject.override.field = parentgrid_flexform_width_column_md_2
                }
                13 {
                    columns_lg.cObject.data = register:columns_md
                    columns_lg.cObject.override.field = parentgrid_flexform_width_column_lg_2
                }

            }
        ...


Frames Sizes
^^^^^^^^^^^^

lib.registerFrameSize is part of lef_responsive_images allowing to take account of frame borders in the layout width. Setting it up to every gridelements is pretty simple.

::

  tt_content.gridelements_pi1.20{
    5 = < lib.registerFrameSize
    10.setup {
        lef_bootstrap_1col < plugin.tx_gridelements_pi1.setup.lef_bootstrap_1col
        lef_bootstrap_2col < plugin.tx_gridelements_pi1.setup.lef_bootstrap_2col
        ...
    }
    15 = < lib.restoreFrameSize
  }