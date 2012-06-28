<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

namespace Nos\Blog;

class Model_Comment extends \Nos\Orm\Model {
    protected static $_table_name = 'nos_comment';
    protected static $_primary_key = array('comm_id');

    protected static $_has_one = array();

    protected static $_belongs_to = array(
        'post' => array(
            'key_from' => 'comm_parent_id',
            'model_to' => 'Nos\Blog\Model_Blog',
            'key_to' => 'blog_id',
            'cascade_save' => false,
            'cascade_delete' => false,
        ),
    );
}
