<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use EWZ\Bundle\RecaptchaBundle\Validator\Constraints as Recaptcha;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use AppBundle\Utils\Upload\UploadProcesor;

/**
 * Guestbook
 *
 * @ORM\Table(name="guestbook")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\GuestbookRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Guestbook
{
  /**
   * @var int
   *
   * @ORM\Column(name="id", type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  private $id;

  /**
   * @var string
   *
   * @ORM\Column(name="name", type="string", length=40)
   *
   * @Assert\NotBlank()
   * @Assert\Length(min=3)
   */
  private $name;

  /**
   * @var string
   *
   * @ORM\Column(name="email", type="string", length=65)
   *
   * @Assert\NotBlank()
   * @Assert\Email(
   *     message = "The email '{{ value }}' is not a valid email.",
   *     checkMX = false
   * )
   */
  private $email;

  /**
   * @var string
   *
   * @ORM\Column(name="website", type="string", nullable=true)
   *
   * @Assert\Url()
   */
  private $website;

  /**
   * @var string
   *
   * @ORM\Column(name="comment", type="text")
   *
   * @Assert\NotBlank()
   * @Assert\Length(min=10, max=400)
   */
  private $comment;

  /**
   * @ORM\Column(type="string", length=255, nullable=true)
   */
  public $path;

  /**
   * @var \DateTime
   *
   * @ORM\Column(name="created_at", type="datetime")
   */
  private $created_at;

  /**
   * @var \DateTime
   *
   * @ORM\Column(name="update_at", type="datetime", nullable=true)
   */
  private $updated_at;

  /**
   * @Recaptcha\IsTrue
   */
  protected $captcha_code;

  /**
   * @Assert\File(
   *      maxSize="1048576",
   *      mimeTypes = {
   *          "image/png",
   *          "image/jpeg",
   *          "image/jpg",
   *          "image/gif"
   *      }
   * )
   */
  private $file;

  /** @var string */
  protected static $uploadDirectory = null;

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
   * Set name
   *
   * @param string $name
   *
   * @return Guestbook
   */
  public function setName($name)
  {
    $this->name = $name;

    return $this;
  }

  /**
   * Get name
   *
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }

  /**
   * Set email
   *
   * @param string $email
   *
   * @return Guestbook
   */
  public function setEmail($email)
  {
    $this->email = $email;

    return $this;
  }

  /**
   * Get email
   *
   * @return string
   */
  public function getEmail()
  {
    return $this->email;
  }

  /**
   * Set website
   *
   * @param string $website
   *
   * @return Guestbook
   */
  public function setWebsite($website)
  {
    $this->website = $website;

    return $this;
  }

  /**
   * Get website
   *
   * @return string
   */
  public function getWebsite()
  {
    return $this->website;
  }

  /**
   * Set comment
   *
   * @param string $comment
   *
   * @return Guestbook
   */
  public function setComment($comment)
  {
    $this->comment = $comment;

    return $this;
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
   * @param \DateTime $createdAt
   *
   * @return Guestbook
   */
  public function setCreatedAt($createdAt)
  {
    $this->created_at = $createdAt;

    return $this;
  }

  /**
   * Get createdAt
   *
   * @return \DateTime
   */
  public function getCreatedAt()
  {
    return $this->created_at;
  }

  /**
   * Set updatedAt
   *
   * @param \DateTime $updatedAt
   *
   * @return Guestbook
   */
  public function setUpdatedAt($updatedAt)
  {
    $this->updated_at = $updatedAt;

    return $this;
  }

  /**
   * Get updatedAt
   *
   * @return \DateTime
   */
  public function getUpdatedAt()
  {
    return $this->updated_at;
  }

  /**
   * setCaptchaCode
   *
   * @param $captchaCode
   */
  public function setCaptchaCode($captchaCode)
  {
    $this->captcha_code = $captchaCode;
  }

  /**
   * get captcha code
   *
   * @return mixed
   */
  public function getCaptchaCode()
  {
    return $this->captcha_code;
  }

  /**
   * Assumes 'type' => 'file'
   */
  public function setFile(File $file)
  {
    $this->file = $file;
  }

  public function getFile()
  {
    //return new File(self::getUploadDirectory() . "/" . $this->path);
    return $this->file;
  }

  /**
   * Gets triggered only on insert
   * @ORM\PrePersist
   */
  public function onPrePersist()
  {
    $this->created_at = new \DateTime("now");
    $this->updated_at = new \DateTime("now");
  }

  /**
   * Gets triggered every time on update
   * @ORM\PreUpdate
   */
  public function onPreUpdate()
  {
    $this->updated_at = new \DateTime("now");
  }

  static public function setUploadDirectory($dir)
  {
    self::$uploadDirectory = $dir;
  }

  static public function getUploadDirectory()
  {
    if (self::$uploadDirectory === null) {
      throw new \RuntimeException("Trying to access upload directory for profile files");
    }
    return self::$uploadDirectory;
  }

  public function setPath($path)
  {
    return $this->path = $path;
  }

  public function getPath()
  {
    return $this->path;
  }

  public function processFile()
  {
    if (! ($this->file instanceof UploadedFile) ) {
      return false;
    }
    $uploadFileMover = new UploadProcesor();
    $this->path = $uploadFileMover->moveUploadedFile($this->file, self::getUploadDirectory());
  }
}
