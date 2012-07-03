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

use Nos\Controller;

class Controller_Admin_Blog extends \Nos\Controller_Admin_Crud {


    protected function crud_item($id)
    {
        return $id === null ? Model_Blog::forge() : Model_Blog::find($id);
    }

    public function action_form($id = null)
    {
        $date = new \Date();
        $date = $date->format('%Y-%m-%d %H:%M:%S');

        if ($id === null) {
            $blog = Model_Blog::forge();
            $blog->author = \Session::user();
            $blog->blog_lang = 'fr_FR'; // default selected language...
            $blog->blog_created_at = $date;
        } else {
            $blog = Model_Blog::find($id);
        }

        $is_new = $blog->is_new();


        if ($is_new) {
            $create_from_id = \Input::get('create_from_id', 0);
            if (empty($create_from_id)) {
                $blog                 = Model_Blog::forge();
                $blog->blog_lang_common_id = \Input::get('common_id');
            } else {
                $object_from = Model_Blog::find($create_from_id);
                $blog      = clone $object_from;
                $blog->tags = $object_from->tags;

                //$blog->wysiwygs = new \Nos\Orm\Model_Wysiwyg_Provider($blog);
                //\Debug::dump($blog->wysiwygs->content);

                //$blog->wysiwygs->content = $object_from->wysiwygs->content; //$wysiwyg;
            }
            $blog->blog_lang = \Input::get('lang');
            $blog->author = \Session::user();
            $blog->blog_created_at = $date;
        }


        $fields = \Config::load('noviusos_blog::controller/admin/form', true);
        \Arr::set($fields, 'author->user_fullname.form.value', $blog->author->fullname());

        if ($is_new || \Input::post('blog_lang', false) != false) {
            $fields = \Arr::merge($fields, array(
                'blog_lang' => array(
                    'form' => array(
                        'type' => 'hidden',
                        'value' => \Input::get('lang'),
                    ),
                ),
                'blog_lang_common_id' => array(
                    'form' => array(
                        'type' => 'hidden',
                        'value' => $blog->blog_lang_common_id,
                    ),
                ),
                'save' => array(
                    'form' => array(
                        'value' => __('Add'),
                    ),
                ),
            ));
        }

        $fieldset = \Fieldset::build_from_config($fields, $blog, array(
            'success' => function($object, $data) use ($is_new) {
                $return = array(
                    'notify' =>  __($is_new ? 'Post successfully added.' : 'Post successfully saved.'),
                    'dispatchEvent' => 'reload.noviusos_blog',
                );
                if ($is_new) {
                    $return['replaceTab'] = 'admin/noviusos_blog/form/crud/'.$object->blog_id;
                }
                return $return;
            },
        ));

        $fieldset->js_validation();

        $return = '';
        if ($blog::behaviours('Nos\Orm_Behaviour_Sharable')) {
            $return .= (string) \Request::forge('nos/admin/datacatcher/form')->execute(array($blog));
        }

        $return .= (string) \View::forge('noviusos_blog::form/form', array(
            'blog'     => $blog,
            'url_crud'  => 'admin/noviusos_blog/blog/crud',
            'fieldset' => $fieldset,
            'lang'     => $blog->blog_lang,
            'tabInfos' => $this->get_tabInfos($blog),
        ), false);

        return $return;
    }
}