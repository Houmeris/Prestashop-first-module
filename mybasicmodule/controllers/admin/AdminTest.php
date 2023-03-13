<?php

require_once(_PS_MODULE_DIR_ . 'mybasicmodule/classes/comment.class.php');
class AdminTestController extends ModuleAdminController
{

    public function initContent()
    {
        parent::initContent();
        $content = "Hello from planet earth";
        $this->context->smarty->assign(
            [
                'content' => $this->content . $content
            ]
        );   
    }

    public function __construct()
    {
        $this->table = "testcomment";
        $this->className = 'CommentTest';
        $this->identifier = CommentTest::$definition['primary'];
        $this->bootstrap = true;
        $this->fields_list = [
            'id' => [
                'title' => 'The id',
                'align' => 'left'
            ],
            'user_id' => [
                'title' => 'The user id',
                'align' => 'left'
            ],
            'comment' => [
                'title' => 'The comment',
                'align' => 'left'
            ]
        ];

        $this->addRowAction('edit');
        $this->addRowAction('delete');
        $this->addRowAction('view');
        parent::__construct();
    }

    public function renderForm()
    {
        $this->fields_form = [
            'legend' => [
                'title' => 'New comment',
                'icon' => 'icon-cog'
            ],
            'input' => [
                [
                    'type' => 'text',
                    'label' => 'The user',
                    'name' => 'user_id',
                    'class' => 'input fixed-with-sm',
                    'required' => true,
                    'empty_message' => 'Please fill the input'
                ],
                [
                    'type' => 'text',
                    'label' => 'The comment',
                    'name' => 'comment',
                    'class' => 'input fixed-with-sm',
                    'required' => true,
                    'empty_message' => 'Please fill the comment'
                ]
            ],
            'submit' => [
                'title' => 'submit the comment'
            ]
        ];

        return parent::renderForm();
    }

    public function renderView()
    {
        $tplFile = dirname(__FILE__) . '/../../views/templates/admin/view.tpl';
        $tpl = $this->context->smarty->createTemplate($tplFile);
        // fetch data
        $sql = new DbQuery();
        $sql->select('*')
            ->from($this->table)
            ->where('id = '. Tools::getValue('id'));
        $data = Db::getInstance()->executeS($sql);
        // assign vars
        $tpl->assign([
            'data' => $data[0]
        ]);
        return $tpl->fetch();
    }
}

?>