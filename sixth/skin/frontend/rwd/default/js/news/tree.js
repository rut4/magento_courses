/**
 * Oggetto Web extension for Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade
 * the Oggetto News module to newer versions in the future.
 * If you wish to customize the Oggetto News module for your needs
 * please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Oggetto
 * @package    Oggetto_News
 * @copyright  Copyright (C) 2014 Oggetto Web ltd (http://oggettoweb.com/)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

function newsTree(treeId){
    var tree = $(treeId);
    if(tree){
        tree.addClassName('tree');
        tree.select('ul').each(function(list){
            $(list).hide();
        })
        tree.select('li').each(function(item){
            var children = $(item).childElements().grep(new Selector('ul'));
            if (children.length > 0) {
                var span = new Element('span').addClassName('collapsed');
                span.observe('click', function(el){
                    if ($(this).hasClassName('collapsed')){
                        this.addClassName('expanded');
                        this.removeClassName('collapsed');
                        $(item).childElements().grep(new Selector('ul')).each(function(list){
                            $(list).show();
                        });
                    }
                    else{
                        this.removeClassName('expanded');
                        this.addClassName('collapsed');
                        $(item).childElements().grep(new Selector('ul')).each(function(list){
                            $(list).hide();
                        });
                    }
                });
                $(item).insert({top:span});
            }
        });
    };
};
