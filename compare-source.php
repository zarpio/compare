<?php
/**
 * @author: Muhammad Khalil
 * @created_at May 22, 2016
 */
include 'compare/bootstrap.php';
?>

<html>
<head>
    <title>Comparison Utility by Khalil</title>
    <!-- Google Fonts -->
    <?php if(isConnected()) : ?>
    <link async href="http://fonts.googleapis.com/css?family=Antic" data-generated="http://enjoycss.com" rel="stylesheet" type="text/css"/>
    <link async href="http://fonts.googleapis.com/css?family=Passero%20One" data-generated="http://enjoycss.com" rel="stylesheet" type="text/css"/>
    <link async href="http://fonts.googleapis.com/css?family=Nova%20Flat" data-generated="http://enjoycss.com" rel="stylesheet" type="text/css"/>
    <?php endif; ?>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="compare/assets/css/font-awesome.min.css">

    <!-- Bootstrap -->
    <link rel="stylesheet" href="compare/assets/bootstrap-3.3.6/css/bootstrap.min.css">

    <!-- Datatables -->
    <link rel="stylesheet" href="compare/assets/css/dataTables.bootstrap.min.css">

    <!-- Custom Styles -->
    <link rel="stylesheet" href="compare/assets/css/styles.css">
</head>
<body data-source-file="<?php echo $sourceFile ?>" data-remote-file="<?php echo $remoteFile ?>" data-key="<?php echo KEY ?>">


<div class="container-fluid">

    <div id="loader">
        <span>Please wait...</span>
    </div>

    <div class="row">
        <div class="text-3D">Comparison between Source & Remote</div>

        <a href="#" class="config"><i class="fa fa-cog" aria-hidden="true" style="font-size: 22px;"></i></a>
        <table class="compare-table table">
            <tr>
                <th>Source Path:</th>
                <td><?php echo __DIR__; ?></td>
            </tr>
            <tr>
                <th>Remote Path:</th>
                <td><?php echo $remoteConfig['remotePath']; ?></td>
            </tr>
            <tr>
                <th>Scan List:</th>
                <td>
                    <ul>
                        <?php foreach ($remoteConfig['scanlist'] as $row) {?>
                            <li><?php echo $row; ?></li>
                        <?php }?>
                    </ul>
                </td>
            </tr>
            <tr>
                <th>Ignore List:</th>
                <td>
                    <ul>
                        <?php foreach ($remoteConfig['ignorelist'] as $row) {?>
                            <li><?php echo $row; ?></li>
                        <?php }?>
                    </ul>
                </td>
            </tr>
        </table>
    </div>

    <hr class="style18">

    <?php //region Difference ?>
    <div class="row section" id="difference" data-id="difference-files">
        <div class="col-xs-12">

            <div class="row alert-box">
                <div class="col-xs-12">
                    <div class="alert" role="alert"></div>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-4">
                    <div class="tag-red">
                        Following files have difference
                    </div>
                </div>
                <div class="col-xs-8">
                    <?php if(checkPermission()) : ?>
                    <div class="tools">
                        <a href="#" class="btn-delete-source-all" title="<?php echo DELETE_ALL_SOURCE_TITLE; ?>" data-toggle="tooltip">
                            <i class="fa fa-trash" aria-hidden="true"></i>
                        </a>
                        <a href="#" class="btn-download-all" title="<?php echo DOWNLOAD_ALL_TITLE; ?>" data-toggle="tooltip">
                            <i class="fa fa-download" aria-hidden="true"></i>
                        </a>
                        <a href="#" class="btn-upload-all" title="<?php echo UPLOAD_ALL_TITLE; ?>" data-toggle="tooltip">
                            <i class="fa fa-upload" aria-hidden="true"></i>
                        </a>
                        <a href="#" class="btn-delete-remote-all" title="<?php echo DELETE_ALL_REMOTE_TITLE; ?>" data-toggle="tooltip">
                            <i class="fa fa-trash" aria-hidden="true"></i>
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div><!-- .row -->

            <div class="row">
                <div class="col-xs-12">&nbsp;</div>
            </div><!-- .row -->

            <div class="row">
                <div class="col-xs-12">
                    <table id="difference-files" class="data-table table table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                        <tr>
                            <th class="no-sort"><input type="checkbox" class="toggle-checkboxes"></th>
                            <th>File</th>
                            <th class="no-sort">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($files_different as $file): ?>
                            <tr>
                                <td><input type="checkbox" value="<?php echo $file; ?>"></td>
                                <td><?php echo $file; ?></td>
                                <td>
                                    <div class="tools-inner">
                                        <a href="#" class="btn-copy" title="<?php echo COPY_TITLE; ?>" data-toggle="tooltip">
                                            <i class="fa fa-clipboard" aria-hidden="true"></i>
                                        </a>
                                        <?php if(checkPermission()) : ?>
                                        <a href="#" class="btn-delete-source"
                                           title="<?php echo DELETE_SOURCE_TITLE; ?>" data-toggle="tooltip">
                                            <i class="fa fa-trash" aria-hidden="true"></i>
                                        </a>
                                        <a href="#" class="btn-download" title="<?php echo DOWNLOAD_TITLE; ?>" data-toggle="tooltip">
                                            <i class="fa fa-download" aria-hidden="true"></i>
                                        </a>
                                        <a href="#" class="btn-upload" title="<?php echo UPLOAD_TITLE; ?>" data-toggle="tooltip">
                                            <i class="fa fa-upload" aria-hidden="true"></i>
                                        </a>
                                        <a href="#" class="btn-delete-remote"
                                           title="<?php echo DELETE_REMOTE_TITLE; ?>" data-toggle="tooltip">
                                            <i class="fa fa-trash" aria-hidden="true"></i>
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div><!-- .col-xs-12 -->
            </div><!-- .row -->

        </div><!-- .col-xs-12 -->
    </div><!-- .row #section-matched -->
    <?php //endregion  ?>

    <hr class="style18">

    <?php //region Source Only ?>
    <div class="row section" id="source-only" data-id="source-only-files">
        <div class="col-xs-12">

            <div class="row alert-box">
                <div class="col-xs-12">
                    <div class="alert" role="alert"></div>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-4">
                    <div class="tag-pink">
                        New files at source
                    </div>
                </div>
                <div class="col-xs-8">
                    <?php if(checkPermission()) : ?>
                    <div class="tools">
                        <a href="#" class="btn-delete-source-all" title="<?php echo DELETE_ALL_SOURCE_TITLE; ?>" data-toggle="tooltip">
                            <i class="fa fa-trash" aria-hidden="true"></i>
                        </a>
                        <a href="#" class="btn-upload-all" title="<?php echo UPLOAD_ALL_TITLE; ?>" data-toggle="tooltip">
                            <i class="fa fa-upload" aria-hidden="true"></i>
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div><!-- .row -->

            <div class="row">
                <div class="col-xs-12">&nbsp;</div>
            </div><!-- .row -->

            <div class="row">
                <div class="col-xs-12">
                    <table id="source-only-files" class="data-table table table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                        <tr>
                            <th class="no-sort"><input type="checkbox" class="toggle-checkboxes"></th>
                            <th>File</th>
                            <th class="no-sort">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($files_new_at_source as $file): ?>
                            <tr>
                                <td><input type="checkbox" value="<?php echo $file; ?>"></td>
                                <td><?php echo $file; ?></td>
                                <td>
                                    <div class="tools-inner">
                                        <a href="#" class="btn-copy" title="<?php echo COPY_TITLE; ?>" data-toggle="tooltip">
                                            <i class="fa fa-clipboard" aria-hidden="true"></i>
                                        </a>
                                        <?php if(checkPermission()) : ?>
                                        <a href="#" class="btn-delete-source"
                                           title="<?php echo DELETE_SOURCE_TITLE; ?>" data-toggle="tooltip">
                                            <i class="fa fa-trash" aria-hidden="true"></i>
                                        </a>
                                        <a href="#" class="btn-upload" title="<?php echo UPLOAD_TITLE; ?>" data-toggle="tooltip">
                                            <i class="fa fa-upload" aria-hidden="true"></i>
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div><!-- .col-xs-12 -->
            </div><!-- .row -->

        </div><!-- .col-xs-12 -->
    </div><!-- .row #section-matched -->
    <?php //endregion ?>

    <hr class="style18">

    <?php //region Remote Only ?>
    <div class="row section" id="remote-only" data-id="remote-only-files">
        <div class="col-xs-12">

            <div class="row alert-box">
                <div class="col-xs-12">
                    <div class="alert" role="alert"></div>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-4">
                    <div class="tag-orange">
                        New files at remote
                    </div>
                </div>
                <div class="col-xs-8">
                    <?php if(checkPermission()) : ?>
                    <div class="tools">
                        <a href="#" class="btn-download-all" title="<?php echo DOWNLOAD_ALL_TITLE; ?>" data-toggle="tooltip">
                            <i class="fa fa-download" aria-hidden="true"></i>
                        </a>
                        <a href="#" class="btn-delete-remote-all" title="<?php echo DELETE_ALL_REMOTE_TITLE; ?>" data-toggle="tooltip">
                            <i class="fa fa-trash" aria-hidden="true"></i>
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div><!-- .row -->

            <div class="row">
                <div class="col-xs-12">&nbsp;</div>
            </div><!-- .row -->

            <div class="row">
                <div class="col-xs-12">
                    <table id="remote-only-files" class="data-table table table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                        <tr>
                            <th class="no-sort"><input type="checkbox" class="toggle-checkboxes"></th>
                            <th>File</th>
                            <th class="no-sort">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($files_new_at_remote as $file): ?>
                            <tr>
                                <td><input type="checkbox" value="<?php echo $file; ?>"></td>
                                <td><?php echo $file; ?></td>
                                <td>
                                    <div class="tools-inner">
                                        <a href="#" class="btn-copy" title="<?php echo COPY_TITLE; ?>" data-toggle="tooltip">
                                            <i class="fa fa-clipboard" aria-hidden="true"></i>
                                        </a>
                                        <?php if(checkPermission()) : ?>
                                        <a href="#" class="btn-download" title="<?php echo DOWNLOAD_TITLE; ?>" data-toggle="tooltip">
                                            <i class="fa fa-download" aria-hidden="true"></i>
                                        </a>
                                        <a href="#" class="btn-delete-remote"
                                           title="<?php echo DELETE_REMOTE_TITLE; ?>" data-toggle="tooltip">
                                            <i class="fa fa-trash" aria-hidden="true"></i>
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div><!-- .col-xs-12 -->
            </div><!-- .row -->

        </div><!-- .col-xs-12 -->
    </div><!-- .row #section-matched -->
    <?php //endregion ?>

    <hr class="style18">

    <?php //region Matched ?>
    <div class="row section" id="matched" data-id="matched-files">
        <div class="col-xs-12">

            <div class="row alert-box">
                <div class="col-xs-12">
                    <div class="alert" role="alert"></div>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-4">
                    <div class="tag-green">
                        Following files are same
                    </div>
                </div>
                <div class="col-xs-8">
                    <?php if(checkPermission()) : ?>
                    <div class="tools">
                        <a href="#" class="btn-delete-source-all" title="<?php echo DELETE_ALL_SOURCE_TITLE; ?>" data-toggle="tooltip">
                            <i class="fa fa-trash" aria-hidden="true"></i>
                        </a>
                        <a href="#" class="btn-download-all" title="<?php echo DOWNLOAD_ALL_TITLE; ?>" data-toggle="tooltip">
                            <i class="fa fa-download" aria-hidden="true"></i>
                        </a>
                        <a href="#" class="btn-upload-all" title="<?php echo UPLOAD_ALL_TITLE; ?>" data-toggle="tooltip">
                            <i class="fa fa-upload" aria-hidden="true"></i>
                        </a>
                        <a href="#" class="btn-delete-remote-all" title="<?php echo DELETE_ALL_REMOTE_TITLE; ?>" data-toggle="tooltip">
                            <i class="fa fa-trash" aria-hidden="true"></i>
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div><!-- .row -->

            <div class="row">
                <div class="col-xs-12">&nbsp;</div>
            </div><!-- .row -->

            <div class="row">
                <div class="col-xs-12">
                    <table id="matched-files" class="data-table table table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                        <tr>
                            <th class="no-sort"><input type="checkbox" class="toggle-checkboxes"></th>
                            <th>File</th>
                            <th class="no-sort">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($files_matched as $file): ?>
                            <tr>
                                <td><input type="checkbox" value="<?php echo $file; ?>"></td>
                                <td><?php echo $file; ?></td>
                                <td>
                                    <div class="tools-inner">
                                        <a href="#" class="btn-copy" title="<?php echo COPY_TITLE; ?>" data-toggle="tooltip">
                                            <i class="fa fa-clipboard" aria-hidden="true"></i>
                                        </a>
                                        <?php if(checkPermission()) : ?>
                                        <a href="#" class="btn-delete-source"
                                           title="<?php echo DELETE_SOURCE_TITLE; ?>" data-toggle="tooltip">
                                            <i class="fa fa-trash" aria-hidden="true"></i>
                                        </a>
                                        <a href="#" class="btn-download" title="<?php echo DOWNLOAD_TITLE; ?>" data-toggle="tooltip">
                                            <i class="fa fa-download" aria-hidden="true"></i>
                                        </a>
                                        <a href="#" class="btn-upload" title="<?php echo UPLOAD_TITLE; ?>" data-toggle="tooltip">
                                            <i class="fa fa-upload" aria-hidden="true"></i>
                                        </a>
                                        <a href="#" class="btn-delete-remote"
                                           title="<?php echo DELETE_REMOTE_TITLE; ?>" data-toggle="tooltip">
                                            <i class="fa fa-trash" aria-hidden="true"></i>
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div><!-- .col-xs-12 -->
            </div><!-- .row -->

        </div><!-- .col-xs-12 -->
    </div><!-- .row #section-matched -->
    <?php //endregion ?>

    <footer class="footer">
        <br><br><br>
        <p class="text-muted" style="text-align: center;">Developed By: Khalil - http://www.muhammadkhalil.com</p>
    </footer>

</div><!-- .container -->

<!-- Jquery -->
<script type="text/javascript" src="compare/assets/js/jquery.min.js"></script>

<!-- Custom Plugin for tooltip -->
<script type="text/javascript" src="compare/assets/js/tooltip.tutsplus.js"></script>

<!-- Custom Plugin for Checkboxes Relationship -->
<script type="text/javascript" src="compare/assets/js/toggleCheckboxes.js"></script>

<!-- Custom Plugin for scroll -->
<script type="text/javascript" src="compare/assets/js/scrollThisTo.js"></script>

<!-- Datatables -->
<script type="text/javascript" src="compare/assets/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="compare/assets/js/dataTables.bootstrap.min.js"></script>

<script type="text/javascript" src="compare/assets/bootstrap-3.3.6/js/tooltip.js"></script>

<!-- Custom  -->
<script type="text/javascript" src="compare/assets/js/app.js"></script>

</body>
</html>