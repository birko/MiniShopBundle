<?php

namespace Core\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Nws\ShopBundle\Entity\Order
 *
 * @ORM\Table(name="coreorder")
 * @ORM\Entity(repositoryClass="Core\ShopBundle\Entity\OrderRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Order
{

    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var decimal $price
     *
     * @ORM\Column(name="price", type="decimal", precision=10, scale=6 )
     */
    protected $price;

    /**
     * @var decimal $priceVAT
     *
     * @ORM\Column(name="price_vat", type="decimal", precision=10, scale=6 )
     */
    protected $priceVAT;

    /**
     * @var string $delivery_name
     *
     * @ORM\Column(name="delivery_name", type="string", length=255)
     */
    private $delivery_name;

    /**
     * @var string $delivery_surname
     *
     * @ORM\Column(name="delivery_surname", type="string", length=255)
     */
    private $delivery_surname;

    /**
     * @var string $delivery_company
     *
     * @ORM\Column(name="delivery_company", type="string", length=255, nullable = true)
     */
    private $delivery_company;

    /**
     * @var string $delivery_email
     *
     * @ORM\Column(name="delivery_email", type="string", length=255, nullable = true)
     */
    private $delivery_email;

    /**
     * @var string $delivery_phone
     *
     * @ORM\Column(name="delivery_phone", type="string", length=255, nullable = true)
     */
    private $delivery_phone;

    /**
     * @var string $delivery_street
     *
     * @ORM\Column(name="delivery_street", type="string", length=255)
     */
    private $delivery_street;

    /**
     * @var string $delivery_housenumber
     *
     * @ORM\Column(name="delivery_housenumber", type="string", length=255)
     */
    private $delivery_housenumber;

    /**
     * @var string $delivery_city
     *
     * @ORM\Column(name="delivery_city", type="string", length=255)
     */
    private $delivery_city;

    /**
     * @var string $delivery_zip
     *
     * @ORM\Column(name="delivery_zip", type="string", length=255)
     */
    private $delivery_zip;

   /**
     * @ORM\ManyToOne(targetEntity="Core\ShopBundle\Entity\State")
     * @ORM\JoinColumn(name="delivery_state_id", referencedColumnName="id")
     */
    private $delivery_state;

    /**
     * @var string $invoice_name
     *
     * @ORM\Column(name="invoice_name", type="string", length=255)
     */
    private $invoice_name;

    /**
     * @var string $invoice_surname
     *
     * @ORM\Column(name="invoice_surname", type="string", length=255)
     */
    private $invoice_surname;

    /**
     * @var string $invoice_company
     *
     * @ORM\Column(name="invoice_company", type="string", length=255, nullable = true)
     */
    private $invoice_company;

    /**
     * @var string $invoice_email
     *
     * @ORM\Column(name="invoice_email", type="string", length=255, nullable = true)
     */
    private $invoice_email;

    /**
     * @var string $invoice_phone
     *
     * @ORM\Column(name="invoice_phone", type="string", length=255, nullable = true)
     */
    private $invoice_phone;

    /**
     * @var string $invoice_street
     *
     * @ORM\Column(name="invoice_street", type="string", length=255)
     */
    private $invoice_street;

    /**
     * @var string $invoice_housenumber
     *
     * @ORM\Column(name="invoice_housenumber", type="string", length=255)
     */
    private $invoice_housenumber;

    /**
     * @var string $invoice_city
     *
     * @ORM\Column(name="invoice_city", type="string", length=255)
     */
    private $invoice_city;

    /**
     * @var string $invoice_zip
     *
     * @ORM\Column(name="invoice_zip", type="string", length=255)
     */
    private $invoice_zip;

     /**
     * @ORM\ManyToOne(targetEntity="Core\ShopBundle\Entity\State")
     * @ORM\JoinColumn(name="invoice_state_id", referencedColumnName="id")
     */
    private $invoice_state;

    /**
     * @var string $invoice_TIN
     *
     * @ORM\Column(name="invoice_tin", type="string", length=20, nullable = true)
     */
    private $invoice_TIN;

    /**
     * @var string $invoice_OIN
     *
     * @ORM\Column(name="invoice_oin", type="string", length=20, nullable = true)
     */
    private $invoice_OIN;

    /**
     * @var string $invoice_VATIN
     *
     * @ORM\Column(name="invoice_vatin", type="string", length=20, nullable = true)
     */
    private $invoice_VATIN;

    /**
     * @var string $comment
     *
     * @ORM\Column(name="comment", type="text", nullable = true)
     */
    private $comment;

     /**
     * @ORM\ManyToOne(targetEntity="Core\ShopBundle\Entity\ShippingStatus")
     * @ORM\JoinColumn(name="shipping_status_id", referencedColumnName="id")
     */
    private $shipping_status;

    /**
     * @ORM\ManyToOne(targetEntity="Core\ShopBundle\Entity\Shipping")
     * @ORM\JoinColumn(name="shipping_id", referencedColumnName="id")
     */
    private $shipping;

    /**
     * @ORM\ManyToOne(targetEntity="Core\ShopBundle\Entity\Payment")
     * @ORM\JoinColumn(name="payment_id", referencedColumnName="id")
     */
    private $payment;

    /**
     * @ORM\ManyToOne(targetEntity="Core\ShopBundle\Entity\OrderStatus")
     * @ORM\JoinColumn(name="order_status_id", referencedColumnName="id")
     */
    private $order_status;

     /**
     * @ORM\Column(name="invoice_status", type="string", length=255, nullable = true)
     */
    private $invoice_status;

    /**
     * @var string $invoice_number
     *
     * @ORM\Column(name="invoice_number", type="string", length=255, nullable = true)
     */
    private $invoice_number;

    /**
     * @var string $variable_number
     *
     * @ORM\Column(name="variable_number", type="string", length=255, nullable = true)
     */
    private $variable_number;

    /**
     * @var string $order_number
     *
     * @ORM\Column(name="order_number", type="string", length=255, nullable = true)
     */
    private $order_number;

    /**
     * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var datetime $createdAt
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var datetime $updatedAt
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable = true)
     */
    private $updatedAt;

    /**
     * @var datetime $invoicedAt
     *
     * @ORM\Column(name="invoiced_at", type="datetime", nullable = true)
     */
    private $invoicedAt;

    /**
     * @var string $dueDays
     *
     * @ORM\Column(name="due_days", type="integer", nullable = true)
     */
    private $dueDays;

    /**
     * @ORM\OneToMany(targetEntity="Core\ShopBundle\Entity\OrderItem", mappedBy="order", cascade={"persist", "remove"})
     * @ORM\OrderBy({ "id" = "ASC"})
     */
    protected $items;

    /**
     * @var string $tracking_id
     *
     * @ORM\Column(name="tracking_id", type="string", length=255, nullable=true)
     */
    private $tracking_id;

    /**
     * @var string
     *
     * @ORM\Column(name="options", type="text", nullable=true)
     */
    private $options;

    public function __construct()
    {
        $this->setCreatedAt(new \DateTime());
        $this->items = new ArrayCollection();
        $this->setOptions(array());
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set price
     *
     * @param decimal $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * Get price
     *
     * @return decimal
     */
    public function getPrice()
    {
        return $this->price;
    }

     /**
     * Set price
     *
     * @param decimal $price
     */
    public function setPriceVAT($price)
    {
        $this->priceVAT = $price;
    }

    /**
     * Get price
     *
     * @return decimal
     */
    public function getPriceVAT()
    {
        return $this->priceVAT;
    }

    /**
     * Set delivery_name
     *
     * @param string $deliveryName
     */
    public function setDeliveryName($deliveryName)
    {
        $this->delivery_name = $deliveryName;
    }

    /**
     * Get delivery_name
     *
     * @return string
     */
    public function getDeliveryName()
    {
        return $this->delivery_name;
    }

    /**
     * Set delivery_surname
     *
     * @param string $deliverySurname
     */
    public function setDeliverySurname($deliverySurname)
    {
        $this->delivery_surname = $deliverySurname;
    }

    /**
     * Get delivery_surname
     *
     * @return string
     */
    public function getDeliverySurname()
    {
        return $this->delivery_surname;
    }

    /**
     * Set delivery_company
     *
     * @param string $deliveryCompany
     */
    public function setDeliveryCompany($deliveryCompany)
    {
        $this->delivery_company = $deliveryCompany;
    }

    /**
     * Get delivery_company
     *
     * @return string
     */
    public function getDeliveryCompany()
    {
        return $this->delivery_company;
    }

    /**
     * Set delivery_email
     *
     * @param string $deliveryEmail
     */
    public function setDeliveryEmail($deliveryEmail)
    {
        $this->delivery_email = $deliveryEmail;
    }

    /**
     * Get delivery_email
     *
     * @return string
     */
    public function getDeliveryEmail()
    {
        return $this->delivery_email;
    }

    /**
     * Set delivery_phone
     *
     * @param string $deliveryPhone
     */
    public function setDeliveryPhone($deliveryPhone)
    {
        $this->delivery_phone = $deliveryPhone;
    }

    /**
     * Get delivery_phone
     *
     * @return string
     */
    public function getDeliveryPhone()
    {
        return $this->delivery_phone;
    }

    /**
     * Set delivery_street
     *
     * @param string $deliveryStreet
     */
    public function setDeliveryStreet($deliveryStreet)
    {
        $this->delivery_street = $deliveryStreet;
    }

    /**
     * Get delivery_street
     *
     * @return string
     */
    public function getDeliveryStreet()
    {
        return $this->delivery_street;
    }

    /**
     * Set delivery_housenumber
     *
     * @param string $deliveryHouseNumber
     */
    public function setDeliveryHouseNumber($deliveryHouseNumber)
    {
        $this->delivery_housenumber = $deliveryHouseNumber;
    }

    /**
     * Get delivery_housenumber
     *
     * @return string
     */
    public function getDeliveryHouseNumber()
    {
        return $this->delivery_housenumber;
    }

    /**
     * Set delivery_city
     *
     * @param string $deliveryCity
     */
    public function setDeliveryCity($deliveryCity)
    {
        $this->delivery_city = $deliveryCity;
    }

    /**
     * Get delivery_city
     *
     * @return string
     */
    public function getDeliveryCity()
    {
        return $this->delivery_city;
    }

    /**
     * Set delivery_zip
     *
     * @param string $deliveryZip
     */
    public function setDeliveryZip($deliveryZip)
    {
        $this->delivery_zip = $deliveryZip;
    }

    /**
     * Get delivery_zip
     *
     * @return string
     */
    public function getDeliveryZip()
    {
        return $this->delivery_zip;
    }

    /**
     * Set delivery_state
     *
     * @param string $deliveryState
     */
    public function setDeliveryState($deliveryState)
    {
        $this->delivery_state = $deliveryState;
    }

    /**
     * Get delivery_state
     *
     * @return string
     */
    public function getDeliveryState()
    {
        return $this->delivery_state;
    }

     /**
     * Set invoice_name
     *
     * @param string $invoiceName
     */
    public function setInvoiceName($invoiceName)
    {
        $this->invoice_name = $invoiceName;
    }

    /**
     * Get invoice_name
     *
     * @return string
     */
    public function getInvoiceName()
    {
        return $this->invoice_name;
    }

    /**
     * Set invoice_surname
     *
     * @param string $invoiceSurname
     */
    public function setInvoiceSurname($invoiceSurname)
    {
        $this->invoice_surname = $invoiceSurname;
    }

    /**
     * Get invoice_surname
     *
     * @return string
     */
    public function getInvoiceSurname()
    {
        return $this->invoice_surname;
    }

    /**
     * Set invoice_company
     *
     * @param string $invoiceCompany
     */
    public function setInvoiceCompany($invoiceCompany)
    {
        $this->invoice_company = $invoiceCompany;
    }

    /**
     * Get invoice_company
     *
     * @return string
     */
    public function getInvoiceCompany()
    {
        return $this->invoice_company;
    }

    /**
     * Set invoice_email
     *
     * @param string $invoiceEmail
     */
    public function setInvoiceEmail($invoiceEmail)
    {
        $this->invoice_email = $invoiceEmail;
    }

    /**
     * Get invoice_email
     *
     * @return string
     */
    public function getInvoiceEmail()
    {
        return $this->invoice_email;
    }

    /**
     * Set invoice_phone
     *
     * @param string $invoicePhone
     */
    public function setInvoicePhone($invoicePhone)
    {
        $this->invoice_phone = $invoicePhone;
    }

    /**
     * Get invoice_phone
     *
     * @return string
     */
    public function getInvoicePhone()
    {
        return $this->invoice_phone;
    }

    /**
     * Set invoice_street
     *
     * @param string $invoiceStreet
     */
    public function setInvoiceStreet($invoiceStreet)
    {
        $this->invoice_street = $invoiceStreet;
    }

    /**
     * Get invoice_street
     *
     * @return string
     */
    public function getInvoiceStreet()
    {
        return $this->invoice_street;
    }

    /**
     * Set invoice_housenumber
     *
     * @param string $invoiceHouseNumber
     */
    public function setInvoiceHouseNumber($invoiceHouseNumber)
    {
        $this->invoice_housenumber = $invoiceHouseNumber;
    }

    /**
     * Get delivery_invoice
     *
     * @return string
     */
    public function getInvoiceHouseNumber()
    {
        return $this->invoice_housenumber;
    }

    /**
     * Set invoice_city
     *
     * @param string $invoiceCity
     */
    public function setInvoiceCity($invoiceCity)
    {
        $this->invoice_city = $invoiceCity;
    }

    /**
     * Get invoice_city
     *
     * @return string
     */
    public function getInvoiceCity()
    {
        return $this->invoice_city;
    }

    /**
     * Set invoice_zip
     *
     * @param string $invoiceZip
     */
    public function setInvoiceZip($invoiceZip)
    {
        $this->invoice_zip = $invoiceZip;
    }

    /**
     * Get invoice_zip
     *
     * @return string
     */
    public function getInvoiceZip()
    {
        return $this->invoice_zip;
    }

    /**
     * Set invoice_state
     *
     * @param string $invoiceState
     */
    public function setInvoiceState($invoiceState)
    {
        $this->invoice_state = $invoiceState;
    }

    /**
     * Get invoice_state
     *
     * @return string
     */
    public function getInvoiceState()
    {
        return $this->invoice_state;
    }

    /**
     * Set comment
     *
     * @param string $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set createdAt
     *
     * @param datetime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * Get createdAt
     *
     * @return datetime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param datetime $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * Get updatedAt
     *
     * @return datetime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAtAt;
    }

    /**
     * Set invoicedAt
     *
     * @param datetime $invoicedAt
     */
    public function setInvoicedAt($invoicedAt)
    {
        $this->invoicedAt = $invoicedAt;
    }

    /**
     * Get invoicedAt
     *
     * @return datetime
     */
    public function getInvoicedAt()
    {
        return $this->invoicedAt;
    }

    /**
     * Set dueDays
     *
     * @param integer $days
     */
    public function setDueDays($days)
    {
        $this->dueDays = $days;
    }

    /**
     * Get dueDays
     *
     * @return integer
     */
    public function getDueDays()
    {
        return $this->dueDays;
    }

    public function getDueAt()
    {
        $date = $this->getInvoicedAt();
        if ($date) {
            $date = clone $date;
            $days = $this->getDueDays();
            if ($days) {
                return $date->add(new \DateInterval("P" . $days. "D"));
            }
        }

        return $date;
    }

    /**
     * Set User
     *
     * @param user $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * Get User
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set shipping_status
     *
     * @param string $shipping_status
     */
    public function setShippingStatus($shipping_status)
    {
        $this->shipping_status= $shipping_status;
    }

    /**
     * Get shipping_status
     *
     * @return string
     */
    public function getShippingStatus()
    {
        return $this->shipping_status;
    }

    /**
     * Set payment
     *
     * @param Payment $payment
     */
    public function setPayment($payment)
    {
        $this->payment= $payment;
    }

    /**
     * Get payment
     *
     * @return Payment
     */
    public function getPayment()
    {
        return $this->payment;
    }

    /**
     * Set shippings
     *
     * @param Shipping $shipping
     */
    public function setShipping($shipping)
    {
        $this->shipping= $shipping;
    }

    /**
     * Get shipping
     *
     * @return Shipping
     */
    public function getShipping()
    {
        return $this->shipping;
    }

    /**
     * Set order_status
     *
     * @param string $order_status
     */
    public function setOrderStatus($order_status)
    {
        $this->order_status= $order_status;
    }

    /**
     * Get shipping_status
     *
     * @return string
     */
    public function getOrderStatus()
    {
        return $this->order_status;
    }

    /**
     * Set invoice_status
     *
     * @param string $invoice_status
     */
    public function setInvoiceStatus($invoice_status)
    {
        $this->invoice_status= $invoice_status;
    }

    /**
     * Get invoice_status
     *
     * @return string
     */
    public function getInvoiceStatus()
    {
        return $this->invoice_status;
    }

    /**
     * Set invoice_number
     *
     * @param string $invoice_number
     */
    public function setInvoiceNumber($invoice_number)
    {
        $this->invoice_number= $invoice_number;
    }

    /**
     * Get invoice_number
     *
     * @return string
     */
    public function getInvoiceNumber()
    {
        return $this->invoice_number;
    }

    /**
     * Set variable_number
     *
     * @param string $variable_status
     */
    public function setVariableNumber($variable_number)
    {
        $this->variable_number= $variable_number;
    }

    /**
     * Get variable_number
     *
     * @return string
     */
    public function getVariableNumber()
    {
        return $this->variable_number;
    }

    /**
     * Add item
     *
     * @param OrderItem $item
     */
    public function addItem($item)
    {
        $this->getItems()->add($item);
    }

    /**
     * Remove item
     *
     * @param OrderItem $item
     */
    public function removeItem($item)
    {
         $this->getItems()->removeElement($item);
    }

    /**
     * Get Items
     *
     * @return ArrayCollection
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Set invoice_TIN
     *
     * @param string $tIN
     */
    public function setInvoiceTIN($tIN)
    {
        $this->invoice_TIN = $tIN;
    }

    /**
     * Get invoice_TIN
     *
     * @return string
     */
    public function getInvoiceTIN()
    {
        return $this->invoice_TIN;
    }

    /**
     * Set invoice_OIN
     *
     * @param string $oIN
     */
    public function setInvoiceOIN($oIN)
    {
        $this->invoice_OIN = $oIN;
    }

    /**
     * Get invoice_OIN
     *
     * @return string
     */
    public function getInvoiceOIN()
    {
        return $this->invoice_OIN;
    }

    /**
     * Set invoice_VATIN
     *
     * @param string $vATIN
     */
    public function setInvoiceVATIN($vATIN)
    {
        $this->invoice_VATIN = $vATIN;
    }

    /**
     * Get invoice_VATIN
     *
     * @return string
     */
    public function getInvoiceVATIN()
    {
        return $this->invoice_VATIN;
    }

     /**
     * Set tracking_id
     *
     * @param string $tracking_id
     */
    public function setTrackingId($trackingId)
    {
        $this->tracking_id = $trackingId;
    }

    /**
     * Get tracking_id
     *
     * @return string
     */
    public function getTrackingId()
    {
        return $this->tracking_id;
    }

    /**
     * Get order_number
     *
     * @return string
     */
    public function getOrderNumber()
    {
        return $this->order_number;
    }

    /**
     * @ORM\PostPersist()
     */
    public function updateOrderNumber()
    {
        $this->order_number = $this->getCreatedAt()->format("ym") . str_pad($this->getId(), 5, "0", STR_PAD_LEFT);
    }

    public function setDeliveryAddress(Address $address)
    {
        if ($address != null) {
            $this->setDeliveryCity($address->getCity());
            $this->setDeliveryEmail($address->getEmail());
            $this->setDeliveryPhone($address->getPhone());
            $this->setDeliveryState($address->getState());
            $this->setDeliveryStreet($address->getStreet());
            $this->setDeliveryHouseNumber($address->getHouseNumber());
            $this->setDeliveryZip($address->getZIP());
            $this->setDeliveryName($address->getName());
            $this->setDeliverySurname($address->getSurname());
            $this->setDeliveryCompany($address->getCompany());
        }
    }

    public function getDeliveryAddress()
    {
        $address = new Address();
        $address->setCity($this->getDeliveryCity());
        $address->setEmail($this->getDeliveryEmail());
        $address->setPhone($this->getDeliveryPhone());
        $address->setState($this->getDeliveryState());
        $address->setStreet($this->getDeliveryStreet());
        $address->setHouseNumber($this->getDeliveryHouseNumber());
        $address->setZIP($this->getDeliveryZip());
        $address->setName($this->getDeliveryName());
        $address->setSurname($this->getDeliverySurname());
        $address->setCompany($this->getDeliveryCompany());

        return $address;
    }

    public function setInvoiceAddress(Address $address)
    {
        if ($address != null) {
            $this->setInvoiceCity($address->getCity());
            $this->setInvoiceEmail($address->getEmail());
            $this->setInvoicePhone($address->getPhone());
            $this->setInvoiceState($address->getState());
            $this->setInvoiceStreet($address->getStreet());
            $this->setInvoiceHouseNumber($address->getHouseNumber());
            $this->setInvoiceZip($address->getZIP());
            $this->setInvoiceName($address->getName());
            $this->setInvoiceSurname($address->getSurname());
            $this->setInvoiceCompany($address->getCompany());
            $this->setInvoiceOIN($address->getOIN());
            $this->setInvoiceTIN($address->getTIN());
            $this->setInvoiceVATIN($address->getVATIN());
        }
    }

    public function getInvoiceAddress()
    {
        $address = new Address();
        $address->setCity($this->getInvoiceCity());
        $address->setEmail($this->getInvoiceEmail());
        $address->setPhone($this->getInvoicePhone());
        $address->setState($this->getInvoiceState());
        $address->setStreet($this->getInvoiceStreet());
        $address->setHouseNumber($this->getInvoiceHouseNumber());
        $address->setZIP($this->getInvoiceZip());
        $address->setName($this->getInvoiceName());
        $address->setSurname($this->getInvoiceSurname());
        $address->setCompany($this->getInvoiceCompany());
        $address->setOIN($this->getInvoiceOIN());
        $address->setTIN($this->getInvoiceTIN());
        $address->setVATIN($this->getInvoiceVATIN());

        return $address;
    }

    /**
     * Set options
     *
     * @param  mixed $options
     * @return Order
     */
    public function setOptions($options)
    {
        $this->options = serialize($options);

        return $this;
    }

    /**
     * Get options
     *
     * @return mixed
     */
    public function getOptions()
    {
        return unserialize($this->options);
    }
}
