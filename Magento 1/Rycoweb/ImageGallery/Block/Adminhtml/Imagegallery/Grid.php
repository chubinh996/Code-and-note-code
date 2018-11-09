<?php
/**
* @category Rycoweb
* @package Rycoweb_ImageGallery
*/ 

class Rycoweb_ImageGallery_Block_Adminhtml_Imagegallery_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('imagegalleryGrid');
        $this->setDefaultSort('imagegallery_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setTemplate('imagegallery/grid.phtml');
    }

    protected function _prepareCollection() {
        $collection = Mage::getModel('imagegallery/imagegallery')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $this->addColumn('imagegallery_id', array(
            'header' => Mage::helper('imagegallery')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'imagegallery_id'
        ));

        $this->addColumn('store_id', array(
            'header' => Mage::helper('catalog')->__('Store'),
            'width' => '100px',
            'type' => 'store',
            'store_view' => true,
            'sortable' => false,
            'index' => 'store_id'
        ));

        $this->addColumn('landscapeimage', array(
            'header' => Mage::helper('imagegallery')->__('Landscape Image'),
            'width' => '150px',
            'index' => 'landscapeimage'
        ));

        $this->addColumn('title', array(
            'header' => Mage::helper('imagegallery')->__('Title'),
            'align' => 'left',
            'index' => 'title'
        ));


        $this->addColumn('landscape_description', array(
            'header' => Mage::helper('imagegallery')->__('Description'),
            'align' => 'left',
            'index' => 'landscape_description'
        ));


        $this->addColumn('sort_order', array(
            'header' => Mage::helper('imagegallery')->__('Sort Order'),
            'align' => 'left',
            'index' => 'sort_order'
        ));

        $this->addColumn('status', array(
            'header' => Mage::helper('imagegallery')->__('Status'),
            'align' => 'left',
            'width' => '80px',
            'index' => 'status',
            'type' => 'options',
            'options' => array(
                1 => 'Enabled',
                2 => 'Disabled'
            )
        ));

        $this->addColumn('action', array(
            'header' => Mage::helper('imagegallery')->__('Action'),
            'width' => '100',
            'type' => 'action',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('imagegallery')->__('Edit'),
                    'url' => array(
                        'base' => '*/*/edit'
                    ),
                    'field' => 'id'
                )
            ),
            'filter' => false,
            'sortable' => false,
            'index' => 'stores',
            'is_system' => true
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('imagegallery')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('imagegallery')->__('XML'));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction() {
        $this->setMassactionIdField('imagegallery_id');
        $this->getMassactionBlock()->setFormFieldName('imagegallery');

        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('imagegallery')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('imagegallery')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('imagegallery/status')->getOptionArray();

        array_unshift($statuses, array(
            'label' => '',
            'value' => ''
        ));
        $this->getMassactionBlock()->addItem('status', array(
            'label' => Mage::helper('imagegallery')->__('Change status'),
            'url' => $this->getUrl('*/*/massStatus', array(
                '_current' => true
            )),
            'additional' => array(
                'visibility' => array(
                    'name' => 'status',
                    'type' => 'select',
                    'class' => 'required-entry',
                    'label' => Mage::helper('imagegallery')->__('Status'),
                    'values' => $statuses
                )
            )
        ));
        return $this;
    }

    public function getRowUrl($row) {
        return $this->getUrl('*/*/edit', array(
                    'id' => $row->getId(),
                    'store' => $row->getStoreId()
        ));
    }

}

