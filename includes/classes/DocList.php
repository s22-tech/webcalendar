<?php
/**
 * The Doc class represents a list of Doc objects.
 *
 * @author Craig Knudsen
 * @copyright Craig Knudsen, <cknudsen@cknudsen.com>, http://k5n.us/
 * @license https://gnu.org/licenses/old-licenses/gpl-2.0.html GNU GPL
 *
 * @package WebCalendar\Doc
 */

/**
 * A list of Doc objects.
 */
class DocList {
  var $_count = 0;
  var $_event_id = -1;
  var $_type;
  var $_arr; // array of Doc objects

  /**
    * Get the number of Doc objects in this list.
    * @return int  The number of objects in this list
    */
  function getSize()
  {
    return $this->_count;
  }

  /**
    * Get a specific Doc object by number (0=first)
    * @param int ind  index number (0=first)
    * @return Doc  The Doc object
    */
  function getDoc ( $ind )
  {
    if ( $ind < 0 || $ind > $this->_count )
      return null;
    return $this->_arr[$ind];
  }

  /**
   * Creates a new Doc list for the specified event. This list
   * will be for either attachments ($type='A') or
   * comments ($type='C').
   *
   * @param int    $event_id  The event id
   * @param int    $type  The type of Doc objects ('A' or 'C')
   * @return DocList The new DocList object
   * @access public
   */
  function __construct ( $event_id, $type )
  {
    $this->_event_id = $event_id;
    $this->_type = $type;
    $this->_arr = [];

    if ( $type != 'A' && $type != 'C' )
      die_miserable_death ( "Invalid DocList type '$type'" );

    $sql = Doc::getSQL ( $event_id, $type );

    $res = dbi_execute ( $sql );
    if ( ! $res ) {
      // TODO: exceptions for PHP5
    } else {
      while ( $row = dbi_fetch_row ( $res ) ) {
        $this->_arr[] = new Doc ( $row );
        $this->_count++;
      }
      dbi_free_result ( $res );
    }
  }

  /**
   * Gets the event ID of this list.
   * @return int event ID
   * @access public
   */
  function getEventId() {
    return $this->_event_id;
  }

  /**
   * Gets the event's description
   *
   * @return string The event's description
   *
   * @access public
   */
  function getDescription() {
    return $this->_description;
  }

}
?>
