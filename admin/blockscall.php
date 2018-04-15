<?php
/**
 * tools Module for XOOPS
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       XOOPS Project (https://xoops.org)
 * @license         http://www.fsf.org/copyleft/gpl.html GNU public license
 * @package         tools
 * @since           2.00
 * @author          Susheng Yang <ezskyyoung@gmail.com>
 */

include __DIR__ . '/header.php';
xoops_cp_header();
loadModuleAdminMenu(2, '');

$op                = isset($_REQUEST['op']) ? $_REQUEST['op'] : 'list';
$blockscallHandler = xoops_getModuleHandler('blockscall');
switch ($op) {
    default:
    case 'list':
        /** @var XoopsModuleHandler $moduleHandler */
        $moduleHandler   = xoops_getHandler('module');
        $criteria        = new \Criteria('isactive', 1);
        $generator_list  = $moduleHandler->getList($criteria);
        $fields          = [
            'bid',
            'mid',
            'name',
            'title',
            'desciption',
            'bcachetime',
            'bcachemodel',
            'last_modified'
        ];
        $blockscall_data = $blockscallHandler->getAll(null, $fields, false, false);
        $cachetime       = [
            '0'       => _NOCACHE,
            '30'      => sprintf(_SECONDS, 30),
            '60'      => _MINUTE,
            '300'     => sprintf(_MINUTES, 5),
            '1800'    => sprintf(_MINUTES, 30),
            '3600'    => _HOUR,
            '18000'   => sprintf(_HOURS, 5),
            '86400'   => _DAY,
            '259200'  => sprintf(_DAYS, 3),
            '604800'  => _WEEK,
            '2592000' => _MONTH
        ];
        $cachemodel      = ['0' => _AM_TOOLS_BC_GLOBAL, '1' => _AM_TOOLS_BC_GROUP, '2' => _AM_TOOLS_BC_USER];
        foreach ($blockscall_data as $k => $v) {
            $blockscall_data[$k]['mname']         = $generator_list[$v['mid']];
            $blockscall_data[$k]['bcachetime']    = $cachetime[$v['bcachetime']];
            $blockscall_data[$k]['bcachemodel']   = $cachemodel[$v['bcachemodel']];
            $blockscall_data[$k]['last_modified'] = formatTimestamp($v['last_modified']);
        }
        $template_main = 'tools_admin_blockscall.tpl';
        $xoopsTpl->assign('bc_data', $blockscall_data);
        break;
    case 'new':
        // Modules for blocks to be visible in
        /** @var XoopsModuleHandler $moduleHandler */
        $moduleHandler  = xoops_getHandler('module');
        $criteria       = new \Criteria('isactive', 1);
        $generator_list = $moduleHandler->getList($criteria);
        unset($criteria);
        $generator_list[-1] = _AM_TOOLS_BC_ALLTYPES;
        ksort($generator_list);
        $selgen = \Xmf\Request::getInt('selgen', -1, 'GET');

        //get blocks
        $criteria = new \CriteriaCompo(new \Criteria('mid', 0, '!='));
        if (-1 != $selgen) {
            $criteria->add(new \Criteria('mid', $selgen));
        }
        $fields        = ['bid', 'mid', 'name', 'title'];
        $blocksHandler = xoops_getModuleHandler('xoopsblock');
        $blocks_array  = $blocksHandler->getAll($criteria, $fields, false, false);
        foreach ($blocks_array as $k => $v) {
            $blocks_array[$k]['mname'] = $generator_list[$v['mid']];
        }
        unset($criteria);

        $xoopsTpl->assign('selgen', $selgen);
        $xoopsTpl->assign('modules', $generator_list);
        $xoopsTpl->assign('blocks', $blocks_array);
        $template_main = 'tools_admin_blockscall_new.tpl';
        break;

    case 'create':

        $blocksHandler = xoops_getModuleHandler('xoopsblock');
        $block_obj     = $blocksHandler->get($_GET['bid']);
        $o_block       = $block_obj->getValues();

        if ('' != $o_block['template']) {
            $tplfileHandler = xoops_getHandler('tplfile');
            $btemplate      = $tplfileHandler->find($GLOBALS['xoopsConfig']['template_set'], 'block', $o_block['bid'], '', '', true);
            if (count($btemplate) > 0) {
                $tpl_source = $btemplate[0]->getVar('tpl_source', 'n');
            } else {
                $btemplate2 = $tplfileHandler->find('default', 'block', $o_block['bid'], '', '', true);
                if (count($btemplate2) > 0) {
                    $tpl_source = $btemplate2[0]->getVar('tpl_source', 'n');
                }
            }
        }

        $blockscall_obj = $blockscallHandler->create();
        $blockscall_obj->setVar('bid', $o_block['bid']);
        $blockscall_obj->setVar('mid', $o_block['mid']);
        $blockscall_obj->setVar('options', $o_block['options']);
        $blockscall_obj->setVar('name', $o_block['name']);
        $blockscall_obj->setVar('title', $o_block['title']);
        $blockscall_obj->setVar('content', $o_block['content']);
        $blockscall_obj->setVar('dirname', $o_block['dirname']);
        $blockscall_obj->setVar('func_file', $o_block['func_file']);
        $blockscall_obj->setVar('show_func', $o_block['show_func']);
        $blockscall_obj->setVar('edit_func', $o_block['edit_func']);
        $blockscall_obj->setVar('template', $o_block['template']);
        $blockscall_obj->setVar('tpl_content', $tpl_source);
        $blockscall_obj->setVar('bcachetime', $o_block['bcachetime']);
        $blockscall_obj->setVar('last_modified', time());
        if ($blockscallHandler->insert($blockscall_obj)) {
            redirect_header("blockscall.php?op=edit&amp;bid={$blockscall_obj->getVar('bid')}", 3, sprintf(_AM_TOOLS_BC_CREATESUCCESS, $blockscall_obj->getVar('name')));
        }

        break;

    case 'edit':

        $blockscall_obj          = $blockscallHandler->get($_GET['bid']);
        $block_data              = $blockscall_obj->getValues(null, 'n');
        $block_data['edit_form'] = $blockscall_obj->getOptions();

        $blockoption = !empty($block_data['options']) ? "options=\"{$block_data['options']}\"" : '';
        $cachetime   = 0 != $block_data['bcachetime'] ? ' cachetime=' . $block_data['bcachetime'] : '';
        if ($cachetime) {
            switch ($block_data['bcachemodel']) {
                case 0:
                    $cachemodel = ' cachemodel=global';
                    break;
                case 1:
                    $cachemodel = ' cachemodel=$xoopsUser->getGroups()';
                    break;
                case 2:
                    $cachemodel = ' cachemodel=$xoopsUser';
                    break;
            }
        } else {
            $cachemodel = '';
        }

        $xoblktpl = <<<EOF
<{xoBlkTpl module="{$block_data['dirname']}" file="{$block_data['func_file']}" show_func="{$block_data['show_func']}" {$blockoption}$cachetime$cachemodel}>
{$block_data['tpl_content']}
<{/xoBlkTpl}>
EOF;
        $xoblk    = <<<EOF
<{xoBlk module="{$block_data['dirname']}" file="{$block_data['func_file']}" show_func="{$block_data['show_func']}" $blockoption template="{$block_data['template']}"$cachetime$cachemodel}>
EOF;

        include  dirname(__DIR__) . '/include/blockform.php';

        $xoopsTpl->assign('xoblktpl', $xoblktpl);
        $xoopsTpl->assign('xoblk', $xoblk);

        $template_main = 'tools_admin_blockscall_edit.tpl';

        break;

    case 'save':
        $blockscall_obj = $blockscallHandler->get($_REQUEST['bid']);
        if (isset($_REQUEST['save']) && 'blk' === $_REQUEST['save']) {
            if (isset($_REQUEST['options'])) {
                $options       = $_REQUEST['options'];
                $options_count = count($options);
                if ($options_count > 0) {
                    //Convert array values to comma-separated
                    for ($i = 0; $i < $options_count; ++$i) {
                        if (is_array($options[$i])) {
                            $options[$i] = implode(',', $options[$i]);
                        }
                    }
                    $options = implode('|', $options);
                    $blockscall_obj->setVar('options', $options);
                }
            }
            $blockscall_obj->setVar('desciption', $_REQUEST['desc']);
            $blockscall_obj->setVar('bcachetime', $_REQUEST['bcachetime']);
            $blockscall_obj->setVar('bcachemodel', $_REQUEST['bcachemodel']);
        } elseif (isset($_REQUEST['save']) && 'tpl' === $_REQUEST['save']) {
            $blockscall_obj->setVar('tpl_content', $_REQUEST['tpl_content']);
        } else {
            exit();
        }

        $blockscall_obj->setVar('last_modified', time());
        if ($blockscallHandler->insert($blockscall_obj)) {
            redirect_header("blockscall.php?op=edit&amp;bid={$blockscall_obj->getVar('bid')}", 3, sprintf(_AM_TOOLS_BC_SAVEDSUCCESS, $blockscall_obj->getVar('name')));
        }
        break;

    case 'edittpl':
        $blockscall_obj = $blockscallHandler->get($_REQUEST['bid']);
        $block_data     = $blockscall_obj->getValues(null, 'n');
        require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
        $form = new \XoopsThemeForm(_AM_TOOLS_BC_EDITTPL, 'form', 'blockscall.php', 'post', true);
        $form->addElement(new \XoopsFormLabel(_AM_TOOLS_BC_BLOCK, $block_data['name']));
        $form->addElement(new \XoopsFormTextArea(_AM_TOOLS_BC_TPLSOURCES, 'tpl_content', $block_data['tpl_content'], 10, 80));
        $form->addElement(new \XoopsFormHidden('bid', $block_data['bid']));
        $form->addElement(new \XoopsFormHidden('op', 'save'));
        $form->addElement(new \XoopsFormHidden('save', 'tpl'));
        $button_tray = new \XoopsFormElementTray('', '&nbsp;');
        $button_tray->addElement(new \XoopsFormButton('', 'submitblock', _SUBMIT, 'submit'));
        $form->addElement($button_tray);
        $form->display();
        break;

    case 'delete':
        $blockscall_obj = $blockscallHandler->get($_REQUEST['bid']);
        if (isset($_REQUEST['ok']) && 1 == $_REQUEST['ok']) {
            if (!$GLOBALS['xoopsSecurity']->check()) {
                redirect_header('blockscall.php', 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
            }
            if ($blockscallHandler->delete($blockscall_obj)) {
                redirect_header('blockscall.php', 3, _AM_TOOLS_BC_DELETEDSUCCESS);
            } else {
                echo $blockscall_obj->getHtmlErrors();
            }
        } else {
            xoops_confirm(['ok' => 1, 'id' => $_REQUEST['bid'], 'op' => 'delete'], $_SERVER['REQUEST_URI'], sprintf(_AM_TOOLS_BC_RUSUREDEL, $blockscall_obj->getVar('name')));
        }
        break;
}
$css = '<link rel="stylesheet" type="text/css" media="all" href="' . XOOPS_URL . '/modules/tools/templates/style.css">';
$xoopsTpl->assign('css', $css);
include __DIR__ . '/footer.php';
