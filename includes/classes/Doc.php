<?php
/**
 * The Doc class represents an attachment/comment. This class provides a
 * convenient way to load and access both attachments and comments without the
 * caller needing to know about the underlying database schema.
 *
 * @author Craig Knudsen
 * @copyright Craig Knudsen, <cknudsen@cknudsen.com>, http://k5n.us/
 * @license https://gnu.org/licenses/old-licenses/gpl-2.0.html GNU GPL
 *
 * @package WebCalendar\Doc
 */

/**
 * A Document object.
 * This can represent an event attachment or comment. Currently, a Doc must be
 * associated with an event, but this may change down the road.
 * We could use this object to support category or user icons, for example.
 */
class Doc {
  /**
   * The unique id
   * @var int
   * @access private
   */
  var $_blob_id;

  /**
   * Associated event's id (if any)
   * @var int
   * @access private
   */
  var $_event_id;

  /**
   * The user login of user who created this Doc
   * @var string
   * @access private
   */
  var $_login;

  /**
   * Filename of the doc (not used for comments)
   * @var string
   * @access private
   */
  var $_name;

  /**
   * The description of the doc
   * @var string
   * @access private
   */
  var $_description;

  /**
   * The size of the doc (in bytes)
   * (not used for comments)
   * @var int
   * @access private
   */
  var $_size;

  /**
   * The MIME type of the doc
   * @var string
   * @access private
   */
  var $_mime_type;

  /**
   * Type of object (C=Comment, A=Attachment)
   * @var string
   * @access private
   */
  var $_type;

  /**
   * Date last modified (in YYYYMMDD format)
   * @var int
   * @access private
   */
  var $_mod_date;

  /**
   * Time last modified (in HHMMSS format)
   * @var int
   * @access private
   */
  var $_mod_time;

  /**
   * Are attachments enabled?
   */
  public static function attachmentsEnabled() {
    global $ALLOW_ATTACH;

    return ( ! empty ( $ALLOW_ATTACH ) && $ALLOW_ATTACH == 'Y' );
  }

  /**
   * Are comments enabled?
   */
  public static function commentsEnabled() {
    global $ALLOW_COMMENTS;

    return ( ! empty ( $ALLOW_COMMENTS ) && $ALLOW_COMMENTS == 'Y' );
  }

  /**
   * Provide a list of comma-separated column names
   * for use in a SQL query to the webcal_blob table.
   */
  public static function getColumnList() {
    return 'cal_blob_id, cal_id, cal_login, cal_name, cal_description, '
     . 'cal_size, cal_mime_type, cal_type, cal_mod_date, cal_mod_time';
  }

  /**
   * Create an SQL query to load either attachment or comment meta data
   * for the specified event id.
   *
   * @param int    $event_id  The event id
   * @param string $type      "A" = attachment, "C" = comment
   *
   * @return string  The SQL text
   *
   * @access public
   */
  public static function getSQL ( $event_id, $type = 'A' ) {
    return 'SELECT ' . Doc::getColumnList()
     . ' FROM webcal_blob WHERE cal_id = '
     . $event_id . ' AND cal_type = \'' . $type
     . '\' ORDER BY cal_mod_date DESC, cal_mod_time DESC';
  }

  /**
   * Generate the SQL to load a single doc specified by its unique ID.
   *
   * @param int blid  Unique doc id
   *
   * @return string  The SQL to load the Doc
   */
  public static function getSQLForDocId ( $blid ) {
    return 'SELECT ' . Doc::getColumnList()
     . ' FROM webcal_blob WHERE cal_blob_id = ' . $blid;
  }

  /**
   * Creates a new Doc object.
   * Typical usage (error-checking not shown here): <blockquote><pre>
   *   $res = dbi_execute ( Doc::getSQLForDocId ( $doc_id ) );
   *   $row = dbi_fetch_row ( $res );
   *   $doc = new Doc ( $row );
   *   dbi_free_result ( $res );
   * </pre></blockquote>
   *
   * @param array $row  An array returned from dbi_fetch_row using
   *      the SQL from Doc->getSQL.
   *      The elements of the array should be:  <ul>
   *      <li> Unique BLOB id  </li>
   *      <li> Event id  </li>
   *      <li> Login of owner  </li>
   *      <li> Name of object  </li>
   *      <li> Description  </li>
   *      <li> Size of object (attachments)  </li>
   *      <li> MIME type of object  </li>
   *      <li> Type of object ('A' or 'C')  </li>
   *      <li> Modification date (YYYYMMDD)  </li>
   *      <li> Modification time (YYYYMMDD)  </li>
   *      </ul>
   *
   * @return Doc The new Doc object
   *
   * @access public
   */
  function __construct ( $row ) {
    if ( ! is_array ( $row ) )
      die_miserable_death ( 'Doc constructor called without an array.' );

    $i = 0;
    $this->_blob_id = $row[$i++];
    $this->_event_id = $row[$i++];
    $this->_login = $row[$i++];
    $this->_name = $row[$i++];
    $this->_description = $row[$i++];
    $this->_size = $row[$i++];
    $this->_mime_type = $row[$i++];
    $this->_type = $row[$i++];
    $this->_mod_date = $row[$i++];
    $this->_mod_time = $row[$i++];
  }

  /**
   * Gets the Doc's unique identifier
   *
   * @return int The Doc's unique identifier
   *
   * @access public
   */
  function getId() {
    return $this->_blob_id;
  }

  /**
   * Gets the Doc's event id
   *
   * @return int The Doc's event id
   *
   * @access public
   */
  function getEventId() {
    return $this->_event_id;
  }

  /**
   * Gets the Doc creator's user login
   *
   * @return string The Doc creator's user login
   *
   * @access public
   */
  function getLogin() {
    return $this->_login;
  }

  /**
   * Gets the Doc's name
   *
   * @return string The Doc's name
   *
   * @access public
   */
  function getName() {
    return $this->_name;
  }

  /**
   * Gets the Doc's description
   *
   * @return string The Doc's description
   *
   * @access public
   */
  function getDescription() {
    return $this->_description;
  }

  /**
   * Gets the size of the object
   *
   * @return int The size (in bytes)
   *
   * @access public
   */
  function getSize() {
    return $this->_size;
  }

  /**
   * Gets the Doc's MIME type
   *
   * @return string The Doc's MIME type
   *
   * @access public
   */
  function getMimeType() {
    return $this->_mime_type;
  }

  /**
   * Gets the Doc's type ('A' or 'C')
   *
   * @return string The Doc's type
   *
   * @access public
   */
  function getType() {
    return $this->_type;
  }

  /**
   * Gets the modification date
   *
   * @return int The modification date (in YYYYMMDD format)
   *
   * @access public
   */
  function getModDate() {
    return $this->_mod_date;
  }

  /**
   * Gets the modification time
   *
   * @return int The modification time (in YYYYMMDD format)
   *
   * @access public
   */
  function getModTime() {
    return $this->_mod_time;
  }

  /**
   * Get a summary of this object
   *
   * @return string  A summary of the object
   */
  function getSummary ($target='') {
    $ret = '<a href="doc.php?blid=' . $this->_blob_id . '"' .
     ( empty ( $target ) ? '' : " target=\"$target\"" ) . '>'
     . htmlspecialchars ( $this->_description ) . '</a>' . ' ( '
     . htmlspecialchars ( $this->_name ) . ', ';
    if ( $this->_size < 1024 )
      $ret .= $this->_size . ' ' . translate ( 'bytes' );
    else
    if ( $this->_size < 1048576 ) // 1024 * 1024
      $ret .= sprintf ( " %.1f", ( $this->_size / 1024 ) ) . translate ( 'kb' );
    else
      $ret .= sprintf ( " %.1f", ( $this->_size / 1048576 ) ) . translate ( 'Mb' );

    return $ret . ', ' . date_to_str ( $this->_mod_date, '', false, true ) . ' )';
  }

  /**
   * Get the binary data of the document.
   * This is NOT cached and is only loaded when this function is called.
   * Repeated calls to this function will make repeated calls to the database.
   *
   * @return mixed  binary data of the blob
   */
  function getData() {
    return dbi_get_blob ( 'webcal_blob', 'cal_blob', 'cal_blob_id = '
       . $this->_blob_id );
  }
}

?>
