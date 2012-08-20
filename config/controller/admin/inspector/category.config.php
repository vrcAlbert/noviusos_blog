<?php
return array(
    'models' => array(
        array(
            'model' => 'Nos\BlogNews\Blog\Model_Category',
            'order_by' => 'cat_sort',
            'childs' => array('Nos\BlogNews\Blog\Model_Category'),
            'dataset' => array(
                'id' => 'cat_id',
                'title' => 'cat_title',

            ),
        ),
    ),
    'roots' => array(
        array(
            'model' => 'Nos\BlogNews\Blog\Model_Category',
            'where' => array(array('cat_parent_id', 'IS', \DB::expr('NULL'))),
            'order_by' => 'cat_sort',
        ),
    ),
);