<?php
/**
 * Page Description:
 * Display a timebar view of a single day.
 *
 * Input Parameters: (all are required)
 * month - specify the starting month of the timebar
 * day   - specify the starting day of the timebar
 * year  - specify the starting year of the timebar
 * users - csv of users to include
 *
 * Security:
 * Must have "allow view others" enabled ($ALLOW_VIEW_OTHER) in
 *   System Settings unless the user is an admin user ($is_admin).
 */

require_once 'includes/init.php';
// Don't allow users to use this feature if "allow view others" is disabled.
if ( $ALLOW_VIEW_OTHER == 'N' && ! $is_admin )
  // not allowed...
  exit;

$noXStr = translate ( 'Program Error No XXX specified!' );

$users = getGetValue ( 'users' );
$year = getGetValue ( 'year' );
$month = getGetValue ( 'month' );
$day = getGetValue ( 'day' );

if ($users != htmlentities($users)) {
  echo str_replace ( 'XXX', 'users', $noXStr );
  exit;
}

// Input args in URL.
// users: list of comma-separated users.
if ( empty ( $users ) ) {
  echo str_replace ( 'XXX', translate ( 'user' ), $noXStr );
  exit;
} elseif ( empty ( $year ) ) {
  echo str_replace ( 'XXX', translate ( 'year' ), $noXStr );
  exit;
} elseif ( empty ( $month ) ) {
  echo str_replace ( 'XXX', translate ( 'month' ), $noXStr );
  exit;
} elseif ( empty ( $day ) ) {
  echo str_replace ( 'XXX', translate ( 'day' ), $noXStr );
  exit;
}

print_header (
  ['js/availability.php/false/' . "$month/$day/$year/"
   . getGetValue ( 'form' )], '', 'onload="focus();"', true, false, true );

$next_url = $prev_url = '?users=' . $users;
$time = mktime ( 0, 0, 0, $month, $day, $year );
$date = date ( 'Ymd', $time );
$next_url .= strftime ( '&amp;year=%Y&amp;month=%m&amp;day=%d', $time + 86400 );
$prev_url .= strftime ( '&amp;year=%Y&amp;month=%m&amp;day=%d', $time - 86400 );
$span = ( $WORK_DAY_END_HOUR - $WORK_DAY_START_HOUR ) * 3 + 1;

$users = explode ( ',', $users );

$nextStr = translate ( 'Next' );
$prevStr = translate ( 'Previous' );

echo '
    <div style="width:99%;">
      <a class="prev" href="' . $prev_url
 . '"><img src="images/bootstrap-icons/arrow-left-circle.svg" class="prev" alt="'
 . $prevStr . '"></a>
      <a class="next" href="' . $next_url
 . '"><img src="images/bootstrap-icons/arrow-right-circle.svg" class="next" alt="'
 . $nextStr . '"></a>
      <div class="title">
        <span class="date">';
printf ( "%s, %s %d, %d", weekday_name ( strftime ( "%w", $time ) ),
  month_name ( $month - 1 ), $day, $year );
echo '</span><br>
      </div>
    </div><br>
    <form action="availability.php" method="post">
      ' . csrf_form_key() . daily_matrix ( $date, $users ) . '
    </form>
    ' . print_trailer ( false, true, true );

?>
