<?php
  // WalletPage
  class NXTBridgeWalletPage {
    private $GENESIS = 1385294400;
    private $api;
    private $options;
    // user variables
    private $about;
    private $id;
    private $email;
    private $login;
    private $agree;

    // page showing
    private $page;
    private $url;

    // NXT response
    private $response;
    
    /****************************************************************************************************************/
    private function sendPOST($url, $data) {
      $cc = curl_init();

      curl_setopt($cc, CURLOPT_POST, 1);
      curl_setopt($cc, CURLOPT_URL, $url);
      curl_setopt($cc, CURLOPT_POSTFIELDS, $data);
      curl_setopt($cc, CURLOPT_RETURNTRANSFER, true);

      $response = curl_exec($cc);
      curl_close($cc);

      return $response;


    }
    /****************************************************************************************************************/
    private function sendJsonPOST($url, $data) { 
      $cc = curl_init();
      $proto = 'http:'; if ( is_ssl() ) { $proto = 'https:'; } 

      curl_setopt($cc, CURLOPT_POST, 1);
      curl_setopt($cc, CURLOPT_HTTPHEADER, array('Content-Type: application/json') );
      curl_setopt($cc, CURLOPT_URL, $proto.$url);
      curl_setopt($cc, CURLOPT_POSTFIELDS, $data);
      curl_setopt($cc, CURLOPT_RETURNTRANSFER, true);

      $response = curl_exec($cc);
      curl_close($cc);

      return $response;
    }

    /****************************************************************************************************************/
    private function log($msg) {
      $log = '['.date("F j, Y, g:i a").'] '.$msg.PHP_EOL;
      file_put_contents('/tmp/nxtbridge.log', $log, FILE_APPEND);
    }

    /****************************************************************************************************************/
    public function __construct() {
      add_action( 'admin_menu', array( $this, 'add_menu_page' ) );
      add_action( 'admin_init', array( $this, 'page_init' ) );
    }

    /*****************************************************************************************************************/

    // add TOP-Level page
    public function add_menu_page () {
      add_menu_page(
        'NXTBridge Wallet',
        'NXTBridge Wallet',
        'read', // for all users
        'nxtbridge_wallet',
        array( $this, 'create_user_page' ),
        plugins_url('../img/nxt-icon-menu.png', __FILE__),
        55 // position
      );
    }

    /*****************************************************************************************************************/

    // Options page callback
    public function create_user_page() {
      //************** CHECK POST FIELDS ***********************

      //$this->log(print_r($_POST, true));

      switch ( $_POST['option_page'] ) {
        case 'NXTBridge_agreement':
          if ( isset($_POST['agree']) ) { //**************************************************************** USER CLICK AGREE
            $this->log('Save agree...');

            $reg = array( 'email' => $this->email, 'login' => $this->login, 'other' => $this->about ); 

            $rr = $this->sendJsonPOST( $this->api . '/register/', json_encode($reg) );

            $opt = get_option('NXTBridge_'.$this->id, $opt); 
            $opt['agree'] ='true';
            update_option('NXTBridge_'.$this->id , $opt);

            $this->options = get_option('NXTBridge_'.$this->id, $opt); 
            $this->agree = true; //TODO: Lifehack...
          } 
          break;

        case 'NXTBridge_account':
          if ( isset($_POST['account']) ) {
            //TODO: Need CHECK ACCOUNT is Reed-Solomon...
            $this->log('Save account...');

            $reg = array( 'id' => $this->id, 'account' => $_POST['account'] ); 

            $rr = $this->sendJsonPOST( $this->api . '/update/', json_encode($reg) );

            $opt = get_option('NXTBridge_'.$this->id, $opt); 
            $opt['account'] = $_POST['account']; 
            update_option('NXTBridge_'.$this->id, $opt);

            $this->options = get_option('NXTBridge_'.$this->id, $opt); 
          }
          break;

        case 'NXTBridge_broadcast':
          if ( isset($_POST['signed_transaction']) ) {
            $this->log('Get signed transaction...');


            $proto = 'http:'; if ( is_ssl() ) { $proto = 'https:'; } 

            $tmp = file_get_contents( $proto . $this->api . '/peer' );
            $nxt_node = json_decode($tmp, true);

            $user_message = '';

            if ( $nxt_node ==  false ) { 
              $this->log('Some error while decode API answer');
              $user_message = 'Error was happen while trying to decode answer from nxter API server';
            } else {
              $random_node  = $nxt_node['data'];
              $url = 'http://' . $random_node . ':7876/nxt';
              $data = array('requestType' => 'broadcastTransaction', 'transactionBytes' => $_POST['signed_transaction'] );

              //$this->log( $url );
              //$this->log( print_r($data, true) );

              $rr = $this->sendPOST( $url , $data );

              //$this->log('Send transaction');
              //$this->log(print_r($rr, true));

              $rr_descr = json_decode($rr, true);

              if ( $rr_descr ) {
                $rr_code = $rr_descr['errorCode'];
                switch ($rr_code) {
                  case '4': 
                    $user_message = $rr_descr['errorDescription'];
                    break;

                  default:
                    $user_message = print_r($rr, true);
                }

                if ( $rr_descr['transaction'] ) { 
                  $user_message = 'Transaction successfully sent to the Nxt network. ID: ' . $rr_descr['transaction'];
                }
              }

            }

            $this->response = $user_message;
          }
          break;

        case 'NXTBridge_send':
          //var_dump($_POST);

          $proto = 'http:'; if ( is_ssl() ) { $proto = 'https:'; } 
          if ( isset($_POST['recipient']) && isset($_POST['amount']) ) {

            $tmp = file_get_contents( $proto . $this->api . '/peer' );
            $nxt_node = json_decode($tmp, true);

            $user_message = '';

            if ( $nxt_node ==  false ) { 
              $this->log('Some error while decode API answer');
              $user_message = 'Error was happen while trying to decode answer from nxter API server';
            } else {
              $random_node  = $nxt_node['data'];
              $url = 'http://' . $random_node . ':7876' . '/nxt?requestType=getAccountPublicKey&account=' . $this->options['account']; 

              $pub = file_get_contents( $url );
              $pub_key = json_decode($pub, true); 

              if ( isset($pub_key['publicKey']) ) { 
                $url = 'http://' . $random_node . ':7876/nxt';
                $data = array('requestType' => 'sendMoney', 'recipient' => $_POST['recipient'], 'amountNQT' => $_POST['amount'] * 100000000, 'publicKey' => $pub_key['publicKey'], 'feeNQT' => 100000000, 'deadline' => 720, 'broadcast' => 'false', 'message' => 'Sent using NXTBridge' );

                $rr = $this->sendPOST( $url, $data );

                //var_dump($rr);
                $rr_descr = json_decode($rr, true);

                if ( $rr_descr ) {
                  $rr_code = $rr_descr['errorCode'];
                  switch ($rr_code) {
                    case '4': 
                      $user_message = $rr_descr['errorDescription'];
                      break;

                    default:
                      $user_message = print_r($rr, true);
                  }

                  if ( $rr_descr['unsignedTransactionBytes'] ) { 
                    $user_message = $rr_descr['unsignedTransactionBytes'];
                  }
                }

              } // end isset
            } // end esle
            
            $this->response = $user_message;
            //var_dump($this->response);
          }
          break;

        default:
      }

      //$this->log('Agree: ' + $this->agree );

      $this->page[0] = $this->agree ? '' : '-active';
      $this->page[1] = $this->agree ? '-active' : '';
      $this->page[2] = '';
      $this->page[3] = '';
      $this->page[4] = '';
      
      if ( $this->agree ) { 
        $this->url[1] = '/wp-admin/admin.php?page=nxtbridge_wallet&sub=accounts';
        $this->url[2] = '/wp-admin/admin.php?page=nxtbridge_wallet&sub=broadcast';
        $this->url[3] = '/wp-admin/admin.php?page=nxtbridge_wallet&sub=ledger';
        $this->url[4] = '/wp-admin/admin.php?page=nxtbridge_wallet&sub=send';

        //************************************************************************************************** PAGE SWITCH THERE
        if ( isset($_GET['sub']) ) { 

          $sub = $_GET['sub'];
          $this->page[1] = ''; $this->page[2] = ''; $this->page[3] = ''; $this->page[4] = '';

          switch ( $sub ) {
            case 'accounts':
              $this->page[1] = '-active';
              break;

            case 'broadcast':
              $this->page[2] = '-active';
              break;

            case 'ledger':
              $this->page[3] = '-active';
              break;

            case 'send':
              $this->page[4] = '-active';
              break;

            default:
              $this->page[1] = '-active';

          } // end switch
        } // end if isset
      } // end if $this->agree

      ?>

      <div class='wrap'>
        <h1>NXTBridge Wallet [<?php echo $this->login; ?>]</h1>

        <h2 class='nav-tab-wrapper wp-clearfix'> 
          <?php if ( ! $this->agree ): //************************************************************************************** AGREEMENT ?>
          <a class='nav-tab nav-tab<?php echo $this->page[0]; ?>'>Agreement</a>
          <?php endif; ?>

          <a href='<?php echo $this->url[1]; ?>' class='nav-tab nav-tab<?php echo $this->page[1]; ?>'>Account</a>
          <a href='<?php echo $this->url[2]; ?>' class='nav-tab nav-tab<?php echo $this->page[2]; ?>'>Broadcast</a>
          <a href='<?php echo $this->url[3]; ?>' class='nav-tab nav-tab<?php echo $this->page[3]; ?>'>Ledger</a>
          <a href='<?php echo $this->url[4]; ?>' class='nav-tab nav-tab<?php echo $this->page[4]; ?>'>Send Nxt</a>
        </h2>

        <?php if ( $this->agree == false ): ?>
            <form method='POST' action=''>
              <?php settings_fields('NXTBridge_agreement'); ?>
              <?php do_settings_sections('nxtbridge_wallet');?>
              <?php submit_button('Agree'); ?>
            </form>

        <?php endif; // page[0] ?>

        <?php if ( strlen($this->page[1]) > 0 ) : //*************************************************************************** ACCOUNTS ?>
            <form method='POST' action=''>
              <?php settings_fields('NXTBridge_account'); ?>
              <?php do_settings_sections('nxtbridge_wallet_account');?>
              <?php submit_button('Save'); ?>
            </form>

        <?php endif; // page[1] ?>

        <?php if ( strlen($this->page[2]) > 0 ) : //*************************************************************************** BROADCAST ?>
            <form method='POST' action=''>
              <?php settings_fields('NXTBridge_broadcast'); ?>
              <?php do_settings_sections('nxtbridge_wallet_broadcast');?>
              <?php submit_button('Broadcast'); ?>
            </form>
        <?php endif; // page[2] ?>


        <?php if ( strlen($this->page[3]) > 0 ) : //******************************************************************************** LEDGER ?>
          <?php if ( $this->options['account'] ) : ?>

          <?php echo $this->show_ledger($this->options['account']); ?>
          <?php else: ?> 
            <p>Please, enter your prefered Nxt account in Reed-Solomon style on the 'Account' page.</p>
          <?php endif; ?> 

        <?php endif; // page[3] ?>

        <?php if ( strlen($this->page[4]) > 0 ) : //******************************************************************************** SEND NXT ?>
          <?php if ( $this->options['account'] ) : ?>

            <form method='POST' action=''>
              <?php settings_fields('NXTBridge_send'); ?>
              <?php do_settings_sections('nxtbridge_send_account');?>
              <?php submit_button('Generate unsigned TX bytes'); ?>
            </form>

          <?php else: ?> 
            <p>Please, enter your prefered Nxt account in Reed-Solomon style on the 'Account' page.</p>
          <?php endif; ?> 

        <?php endif; // page[3] ?>



      </div>
      <?php
    }

    /*****************************************************************************************************************/
    private function show_ledger($account) {
      printf("<br><strong>%s</strong>", $account);
    ?>
    <table class='form-table'>
      <thead class='ledger-header'><tr>
        <td>Date</td><td>Type</td><td>Change</td><td>Balance</td><td>From / To</td>
      </tr></thead>
      <?php
        $proto = 'http:'; if ( is_ssl() ) { $proto = 'https:'; } 
        $tmp = file_get_contents( $proto . $this->api . '/peer' );
        $nxt_node = json_decode($tmp, true);
        $random_node  = $nxt_node['data'];
        //TODO: Add default Node if random don't exists

        $url = 'http://' . $random_node . ':7876' . '/nxt?requestType=getAccountLedger&account=' . $account . '&includeHoldingInfo=true&includeTransactions=true&firstIndex=0&lastIndex=20'; 
        $tmp = file_get_contents( $url );
        $tmp = json_decode($tmp, true);

        if ( ! $tmp['entries']  ) {
          ?>
            </table>
            <h4>There is no data.</h4>
          <?php
        } else {
          $ledger = $tmp['entries'];
          for ( $i=0; $i<count($ledger); $i++ ) {
            $timestamp = $ledger[$i]['timestamp'];
            $timestamp_p = gmdate("Y-m-d H:i:s", $timestamp + $this->GENESIS );
            
            $sender = $ledger[$i]['transaction']['senderRS'];
            $recipient = $ledger[$i]['transaction']['recipientRS'];

            if ( $sender == $account ) { $sender = 'You'; }
            if ( $recipient == $account ) { $recipient = 'You'; }
            $fromto = $sender . ' &#x2192; ' . $recipient;

            $type = $ledger[$i]['eventType'];

            switch ( $type ) { 
              case 'ASSET_TRANSFER':
                $asset = $ledger[$i]['holdingInfo']['name'];
                $asset_dec = $ledger[$i]['holdingInfo']['decimals'];

                $change = $ledger[$i]['change'] / pow(10, 8 - $asset_dec); $change .= ' ' . $asset;
                $balance = $ledger[$i]['balance'] / pow(10, 8 - $asset_dec); $balance .= ' ' . $asset;

                $type_p = 'Asset Transfer';

                break;

              case 'TRANSACTION_FEE':
                $change = $ledger[$i]['change'] / pow(10, 8); $change .= ' NXT';
                $balance = $ledger[$i]['balance'] / pow(10, 8); $balance .= ' NXT';

                $type_p = 'Transaction fee';
                $fromto = "You &#x2192; Somebody";
                break;

              case 'CURRENCY_EXCHANGE':
                //var_dump( $ledger[$i] );
                $change = $ledger[$i]['change'] / pow(10, 8); $change .= ' NXT';
                $balance = $ledger[$i]['balance'] / pow(10, 8); $balance .= ' NXT';

                $type_p = 'Currency exchange';
                $fromto = "<span class='ledger-symbols'>&#x221e;</span>";

                break;

              case 'CURRENCY_EXCHANGE_SELL':
                $cur = $ledger[$i]['holdingInfo']['name'];
                $cur_dec = $ledger[$i]['holdingInfo']['decimals'];

                $change = $ledger[$i]['change'] / pow(10, 8 - $cur_dec); $change .= ' ' . $cur;
                $balance = $ledger[$i]['balance'] / pow(10, 8 - $cur_dec); $balance .= ' ' . $cur;

                $type_p = 'Currency sell';
                $fromto = "<span class='ledger-symbols'>&#x221e;</span>";

                break;
                
              case 'CURRENCY_TRANSFER':
                //var_dump( $ledger[$i] );
                $cur = $ledger[$i]['holdingInfo']['name'];
                $cur_dec = $ledger[$i]['holdingInfo']['decimals'];

                $change = $ledger[$i]['change'] / pow(10, 8 - $cur_dec); $change .= ' ' . $cur;
                $balance = $ledger[$i]['balance'] / pow(10, 8 - $cur_dec); $balance .= ' ' . $cur;

                $type_p = 'Currency transfer';

                break;


              case 'ORDINARY_PAYMENT':
                $change = $ledger[$i]['change'] / pow(10, 8); $change .= ' NXT';
                $balance = $ledger[$i]['balance'] / pow(10, 8); $balance .= ' NXT';

                $type_p = 'Payment';

                break;

              case 'BLOCK_GENERATED':
                $change = $ledger[$i]['change'] / pow(10, 8); $change .= ' NXT';
                $balance = $ledger[$i]['balance'] / pow(10, 8); $balance .= ' NXT';

                $fromto = 'Yahoo!';

                $type_p = 'Block generated';

                break;


              default:
                $change = $ledger[$i]['change'] / pow(10, 8); $change .= ' NXT';
                $balance = $ledger[$i]['balance'] / pow(10, 8); $balance .= ' NXT';

                $type_p = $type;

            }

            printf("<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>", $timestamp_p, $type_p,$change,$balance,$fromto);
          }
        } // end if

        //echo print_r($tmp, true);

      ?>
    </table>

    <?php
    }
    /*****************************************************************************************************************/
    // register and add setting
    public  function page_init() {
      global $api;
      $this->api = $api;

      //$this->log('API: 11' + $this->api);

      $current_user = wp_get_current_user();

      $this->email = $current_user->user_email;
      $this->login = $current_user->user_login;
      $this->id = md5($this->email); // Settings ID
      $this->options = get_option('NXTBridge_'.$this->id);                                    //************************************  LOAD USER OPTIONS

      $this->about['site_name'] = get_bloginfo('name');
      $this->about['site_url'] = get_bloginfo('url');
      $this->about['site_language'] = get_bloginfo('language');
      $this->about['site_version'] = get_bloginfo('version');

      //$this->log('Read options: ');
      //$this->log(print_r($this->options, true));

      $this->agree = isset( $this->options['agree'] ) ? true : '';

      //****************************************************************************************************************************** AGREEMENT SECTION 
      register_setting(
        'NXTBridge_agreement',                  // option group
        'NXTBridge_'.$this->id,                 // option name
        array( $this, 'sanitize' )
      );

      add_settings_section(
        'NXTBridge_agreement_section',          // Section name
        'NXTBridge Agreement',                  // Section Title
        array( $this, 'print_agreement_info'),  // Section callback
        'nxtbridge_wallet'                      // Page where Section will be display
      );

      add_settings_field(
        'NXTBridge_user_ageee',                 // Setting ID
        'I agree with Terms',                   // Setting title
        array( $this, 'user_agree_callback' ),  // Setting callback
        'nxtbridge_wallet',                     // Page where setting will be display
        'NXTBridge_agreement_section'           // Section name
      );

      //****************************************************************************************************************************** ACCOUNT SECTION 
      register_setting(
        'NXTBridge_account',                     // option group
        'NXTBridge_'.$this->id,                 // option name
        array( $this, 'sanitize' )
      );

      add_settings_section(
        'NXTBridge_account_section',            // Section name
        'NXTBridge Account section',            // Section Title
        array( $this, 'print_account_info'),    // Section callback
        'nxtbridge_wallet_account'                      // Page where Section will be display
      );

      add_settings_field(
        'NXTBridge_user_account',                 // Setting ID
        'Your NXT account',                       // Setting title
        array( $this, 'user_account_callback' ),  // Setting callback
        'nxtbridge_wallet_account',               // Page where setting will be display
        'NXTBridge_account_section'               // Section name
      );

      //****************************************************************************************************************************** BROADCAST SECTION 

      register_setting(
        'NXTBridge_broadcast',                     // option group
        'NXTBridge_'.$this->id,                 // option name
        array( $this, 'sanitize' )
      );

      add_settings_section(
        'NXTBridge_broadcast_section',            // Section name
        'NXTBridge broadcast transaction',            // Section Title
        array( $this, 'print_broadcast_info'),    // Section callback
        'nxtbridge_wallet_broadcast'                      // Page where Section will be display
      );

      add_settings_field(
        'NXTBridge_broadcast',                      // Setting ID
        'Your SIGNED transaction',                  // Setting title
        array( $this, 'user_broadcast_callback' ),   // Setting callback
        'nxtbridge_wallet_broadcast',                 // Page where setting will be display
        'NXTBridge_broadcast_section'               // Section name
      );

      add_settings_field(
        'NXTBridge_broadcast_result',                      // Setting ID
        'Result',                  // Setting title
        array( $this, 'user_broadcast_result_callback' ),   // Setting callback
        'nxtbridge_wallet_broadcast',                 // Page where setting will be display
        'NXTBridge_broadcast_section'               // Section name
      );

      //****************************************************************************************************************************** SEND NXT SECTION 
      register_setting(
        'NXTBridge_send',                       // option group
        'NXTBridge_'.$this->id,                 // option name
        array( $this, 'sanitize' )
      );

      add_settings_section(
        'NXTBridge_send_section',            // Section name
        'NXTBridge Send NXT section',            // Section Title
        array( $this, 'print_send_info'),    // Section callback
        'nxtbridge_send_account'                      // Page where Section will be display
      );

      add_settings_field(
        'NXTBridge_recipient_account',                  // Setting ID
        'Recipient NXT account',                        // Setting title
        array( $this, 'recipient_account_callback' ),   // Setting callback
        'nxtbridge_send_account',                       // Page where setting will be display
        'NXTBridge_send_section'                        // Section name
      );

      add_settings_field(
        'NXTBridge_amount_account',                  // Setting ID
        'Recipient NXT amount',                        // Setting title
        array( $this, 'amount_account_callback' ),   // Setting callback
        'nxtbridge_send_account',                       // Page where setting will be display
        'NXTBridge_send_section'                        // Section name
      );

      add_settings_field(
        'NXTBridge_tx_account',                  // Setting ID
        'Unsigned TX bytes',                        // Setting title
        array( $this, 'tx_account_callback' ),   // Setting callback
        'nxtbridge_send_account',                       // Page where setting will be display
        'NXTBridge_send_section'                        // Section name
      );
    }


    // Sanitize each settings field as needed
    // $param array $input contains all settings fields as array keys



    /*****************************************************************************************************************/
    //
    //                                      AGREEMENT
    //
    /*****************************************************************************************************************/

    // Print the sections text
    public function print_agreement_info() {
    ?>
      <p>
        <div class='large-text code' > 
          <?php include('AGREEMENT.html'); ?>
        </div>
      </p>
    <?php
    }

    public function user_agree_callback() {
      printf("<input name='agree' type='checkbox' value='%s' />", isset( $this->options['agree'] ) ? 'true' : 'false' );
    }

    /*****************************************************************************************************************/
    //
    //                                      SANITIZE
    //
    /*****************************************************************************************************************/
    public function sanitize( $input ) {
      $new_input = array();
      //TODO: Add Nxt account corrections check
      
      if ( isset( $input['agree'] ) ) { $new_input['agree'] = 'true'; }
      if ( isset( $input['account'] ) ) { $new_input['account'] = sanitize_text_field($input['account']) ; }

      return $new_input;
    }    

    /*****************************************************************************************************************/
    //
    //                                      ACCOUNTS
    //
    /*****************************************************************************************************************/
    public function print_account_info() {
      printf("<p>Enter your (or not) Nxt account in Reed-Solomon format.</p>");
    }
    public function user_account_callback() {
      printf("<input name='account' type='text' size='30' value='%s' />", isset( $this->options['account'] ) ? sanitize_text_field( $this->options['account'] ): '' );
    }

    /*****************************************************************************************************************/
    //
    //                                      BROADCAST
    //
    /*****************************************************************************************************************/
    public function print_broadcast_info() {
      printf("<p>Enter your SIGNED transaction BYTES to broadcast to the Nxt network. You can download <a href='https://bitbucket.org/scor2k/nxtbridge-offline/downloads/index.min.html' target=_blank>NXTBridge-offline</a> to sign your transaction offline.</p>");
    }
    public function user_broadcast_callback() {
      printf("<textarea name='signed_transaction' rows='5' cols='30' class='large-text code'></textarea>");
    }

    public function user_broadcast_result_callback() {
      printf("<input type='text' value='%s' class='large-text code' readonly />", $this->response);
    }

    /*****************************************************************************************************************/
    //
    //                                      SEND NXT
    //
    /*****************************************************************************************************************/
    public function print_send_info() {
      printf("<p>In this section you can generate unsigned TX bytes for send Nxt action. Then you should sign this bytes with <a href='https://bitbucket.org/scor2k/nxtbridge-offline/downloads/index.min.html' target=_blank>NXTBridge-offline</a> and <a href='/wp-admin/admin.php?page=nxtbridge_wallet&sub=broadcast'>broadcast</a> to the Nxt network.</p>");
    }

    public function recipient_account_callback() {
      printf("<input name='recipient' type='text' size='30' value='%s' />", isset( $_COOKIE['NXTBridgeTip_addr'] ) ? sanitize_text_field( $_COOKIE['NXTBridgeTip_addr'] ): '' );
    }

    public function amount_account_callback() {
      printf("<input name='amount' type='text' size='30' value='%s' />", isset( $_COOKIE['NXTBridgeTip_amount'] ) ? sanitize_text_field( $_COOKIE['NXTBridgeTip_amount'] ): '' );
    }

    public function tx_account_callback() {
      printf("<textarea name='tx' rows='5' cols='30' class='large-text code' readonly>%s</textarea>", $this->response );
    }
  } // end class


  $my_wallet_page = new NXTBridgeWalletPage(); 

?>
