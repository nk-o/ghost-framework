<?php
/**
 * Brand SVG Icon and Names
 *
 * https://github.com/nk-o/brand-svg-please
 * v1.1.0
 *
 * @package @@theme_name/ghost
 */

/**
 * Ghost_Framework_Brand_Svg
 */
class Ghost_Framework_Brand_Svg {
    /**
     * Get the SVG string for a given icon.
     *
     * @param string $name - brand name.
     * @param array  $data - svg icon data.
     *
     * @return string
     */
    public static function get( $name, $data = array() ) {
        $brand = self::find_brand( $name );

        if ( $brand ) {
            return self::get_svg_by_path( $brand['svg_path'], $data );
        }

        return null;
    }

    /**
     * Print the SVG string for a given icon.
     *
     * @param string $name - icon name.
     * @param array  $data - svg icon data.
     */
    public static function get_e( $name, $data = array() ) {
        if ( self::exists( $name ) ) {
            echo wp_kses( self::get( $name, $data ), self::kses() );
        }
    }

    /**
     * Get the SVG string for a given icon.
     *
     * @param string $name - brand name.
     *
     * @return string
     */
    public static function get_name( $name ) {
        $brand = self::find_brand( $name );

        if ( $brand ) {
            return $brand['name'];
        }

        return null;
    }

    /**
     * Check if SVG icon exists.
     *
     * @param string $name - brand name.
     *
     * @return boolean
     */
    public static function exists( $name ) {
        return ! ! self::find_brand( $name );
    }

    /**
     * Data for SVG useful in wp_kses function.
     *
     * @return array
     */
    public static function kses() {
        return array(
            'svg'   => array(
                'class'           => true,
                'aria-hidden'     => true,
                'aria-labelledby' => true,
                'role'            => true,
                'focusable'       => true,
                'xmlns'           => true,
                'width'           => true,
                'height'          => true,
                'viewbox'         => true,
            ),
            'g'     => array(
                'fill' => true,
            ),
            'title' => array(
                'title' => true,
            ),
            'path'  => array(
                'd'         => true,
                'fill'      => true,
                'fill-rule' => true,
                'transform' => true,
            ),
            'polygon' => array(
                'fill'      => true,
                'fill-rule' => true,
                'points'    => true,
                'transform' => true,
                'focusable' => true,
            ),
        );
    }

    /**
     * Find brand data.
     *
     * @param string $name - brand name.
     *
     * @return null|array
     */
    private static function find_brand( $name ) {
        $result = null;
        $brands = self::get_all_brands();

        // Find by key.
        if ( isset( $brands[ $name ] ) ) {
            $result = $brands[ $name ];
        }

        // Find by alternative keys.
        if ( ! $result ) {
            foreach ( $brands as $brand ) {
                if ( ! $result && isset( $brand['keys'] ) && in_array( $name, $brand['keys'], true ) ) {
                    $result = $brand;
                }
            }
        }

        return $result;
    }

    /**
     * Get the SVG string for a given icon.
     *
     * @param string $path - icon path.
     * @param array  $data - svg icon data.
     *
     * @return string
     */
    private static function get_svg_by_path( $path, $data = array() ) {
        $data = array_merge(
            array(
                'size'  => 24,
                'class' => 'bsp-icon',
            ),
            $data
        );

        if ( file_exists( $path ) ) {
            // We can't use file_get_contents in WordPress themes.
            ob_start();
            include $path;
            $svg = ob_get_clean();

            // Add extra attributes to SVG code.
            // translators: %1$s - classname.
            // translators: %2$d - size.
            $repl = sprintf( '<svg class="%1$s" width="%2$d" height="%2$d" aria-hidden="true" role="img" focusable="false" ', $data['class'], $data['size'] );
            $svg  = preg_replace( '/^<svg /', $repl, trim( $svg ) );

            return $svg;
        }

        return null;
    }

    /**
     * Get all available brands.
     *
     * @param boolean $get_svg - get SVG and insert it inside array.
     * @param array   $svg_data - svg data.
     *
     * @return array
     */
    public static function get_all_brands( $get_svg = false, $svg_data = array() ) {
        $brands = array(
            '42-group'                  => esc_html__( '42 Group', '@@text_domain' ),
            '500px'                     => esc_html__( '500px', '@@text_domain' ),
            'accusoft'                  => esc_html__( 'Accusoft', '@@text_domain' ),
            'acquisitions-incorporated' => esc_html__( 'Acquisitions Incorporated', '@@text_domain' ),
            'adn'                       => esc_html__( 'ADN', '@@text_domain' ),
            'adobe'                     => esc_html__( 'Adobe', '@@text_domain' ),
            'adversal'                  => esc_html__( 'Adversal', '@@text_domain' ),
            'affiliatetheme'            => esc_html__( 'Affiliate Theme', '@@text_domain' ),
            'airbnb'                    => esc_html__( 'Airbnb', '@@text_domain' ),
            'algolia'                   => esc_html__( 'Algolia', '@@text_domain' ),
            'alipay'                    => esc_html__( 'Alipay', '@@text_domain' ),
            'amazon-pay'                => esc_html__( 'Amazon Pay', '@@text_domain' ),
            'amazon'                    => esc_html__( 'Amazon', '@@text_domain' ),
            'amilia'                    => esc_html__( 'Amilia', '@@text_domain' ),
            'android'                   => esc_html__( 'Android', '@@text_domain' ),
            'angellist'                 => esc_html__( 'AngelList', '@@text_domain' ),
            'angrycreative'             => esc_html__( 'Angry Creative', '@@text_domain' ),
            'angular'                   => esc_html__( 'Angular', '@@text_domain' ),
            'app-store'                 => esc_html__( 'App Store', '@@text_domain' ),
            'app-store-ios'             => esc_html__( 'App Store iOS', '@@text_domain' ),
            'apper'                     => esc_html__( 'Apper', '@@text_domain' ),
            'apple-pay'                 => esc_html__( 'Apple Pay', '@@text_domain' ),
            'apple'                     => esc_html__( 'Apple', '@@text_domain' ),
            'artstation'                => esc_html__( 'ArtStation', '@@text_domain' ),
            'asymmetrik'                => esc_html__( 'Asymmetrik', '@@text_domain' ),
            'atlassian'                 => esc_html__( 'Atlassian', '@@text_domain' ),
            'audible'                   => esc_html__( 'Audible', '@@text_domain' ),
            'autoprefixer'              => esc_html__( 'Autoprefixer', '@@text_domain' ),
            'avianex'                   => esc_html__( 'Avianex', '@@text_domain' ),
            'aviato'                    => esc_html__( 'Aviato', '@@text_domain' ),
            'bandcamp'                  => esc_html__( 'Bandcamp', '@@text_domain' ),
            'battle-net'                => esc_html__( 'Battle.net', '@@text_domain' ),
            'behance'                   => esc_html__( 'Behance', '@@text_domain' ),
            'bilibili'                  => esc_html__( 'Bilibili', '@@text_domain' ),
            'bimobject'                 => esc_html__( 'BIMobject', '@@text_domain' ),
            'bitbucket'                 => esc_html__( 'Bitbucket', '@@text_domain' ),
            'bitcoin'                   => esc_html__( 'Bitcoin', '@@text_domain' ),
            'bity'                      => esc_html__( 'Bity', '@@text_domain' ),
            'black-tie'                 => esc_html__( 'Black Tie', '@@text_domain' ),
            'blackberry'                => esc_html__( 'BlackBerry', '@@text_domain' ),
            'blogger'                   => esc_html__( 'Blogger', '@@text_domain' ),
            'bluetooth'                 => esc_html__( 'Bluetooth', '@@text_domain' ),
            'bootstrap'                 => esc_html__( 'Bootstrap', '@@text_domain' ),
            'bots'                      => esc_html__( 'Bots', '@@text_domain' ),
            'brave'                     => esc_html__( 'Brave', '@@text_domain' ),
            'btc'                       => esc_html__( 'BTC', '@@text_domain' ),
            'buffer'                    => esc_html__( 'Buffer', '@@text_domain' ),
            'buromobelexperte'          => esc_html__( 'Büromöbel Experte', '@@text_domain' ),
            'buy-n-large'               => esc_html__( 'Buy n Large', '@@text_domain' ),
            'buysellads'                => esc_html__( 'BuySellAds', '@@text_domain' ),
            'canadian-maple-leaf'       => esc_html__( 'Canadian Gold Maple Leaf', '@@text_domain' ),
            'cc-amazon-pay'             => esc_html__( 'Amazon Pay', '@@text_domain' ),
            'cc-amex'                   => esc_html__( 'Amex', '@@text_domain' ),
            'cc-apple-pay'              => esc_html__( 'Apple Pay', '@@text_domain' ),
            'cc-diners-club'            => esc_html__( 'Diners Club', '@@text_domain' ),
            'cc-discover'               => esc_html__( 'Discover', '@@text_domain' ),
            'cc-jcb'                    => esc_html__( 'JCB', '@@text_domain' ),
            'cc-mastercard'             => esc_html__( 'Mastercard', '@@text_domain' ),
            'cc-paypal'                 => esc_html__( 'PayPal', '@@text_domain' ),
            'cc-stripe'                 => esc_html__( 'Stripe', '@@text_domain' ),
            'cc-visa'                   => esc_html__( 'Visa', '@@text_domain' ),
            'centercode'                => esc_html__( 'Centercode', '@@text_domain' ),
            'centos'                    => esc_html__( 'CentOS', '@@text_domain' ),
            'chrome'                    => esc_html__( 'Chrome', '@@text_domain' ),
            'chromecast'                => esc_html__( 'Chromecast', '@@text_domain' ),
            'cloudflare'                => esc_html__( 'Cloudflare', '@@text_domain' ),
            'cloudscale'                => esc_html__( 'CloudScale', '@@text_domain' ),
            'cloudsmith'                => esc_html__( 'Cloudsmith', '@@text_domain' ),
            'cloudversify'              => esc_html__( 'Cloudversify', '@@text_domain' ),
            'cmplid'                    => esc_html__( 'Cmplid://', '@@text_domain' ),
            'codepen'                   => esc_html__( 'CodePen', '@@text_domain' ),
            'codiepie'                  => esc_html__( 'CodiePie', '@@text_domain' ),
            'confluence'                => esc_html__( 'Confluence', '@@text_domain' ),
            'connectdevelop'            => esc_html__( 'Connect Develop', '@@text_domain' ),
            'contao'                    => esc_html__( 'Contao', '@@text_domain' ),
            'cotton-bureau'             => esc_html__( 'Cotton Bureau', '@@text_domain' ),
            'cpanel'                    => esc_html__( 'cPanel', '@@text_domain' ),
            'critical-role'             => esc_html__( 'Critical Role', '@@text_domain' ),
            'css3'                      => esc_html__( 'CSS3', '@@text_domain' ),
            'cuttlefish'                => esc_html__( 'Cuttlefish', '@@text_domain' ),
            'd-and-d-beyond'            => esc_html__( 'D&D Beyond', '@@text_domain' ),
            'd-and-d'                   => esc_html__( 'D&D', '@@text_domain' ),
            'dailymotion'               => esc_html__( 'Dailymotion', '@@text_domain' ),
            'dashcube'                  => esc_html__( 'Dashcube', '@@text_domain' ),
            'debian'                    => esc_html__( 'Debian', '@@text_domain' ),
            'deezer'                    => esc_html__( 'Deezer', '@@text_domain' ),
            'delicious'                 => esc_html__( 'Delicious', '@@text_domain' ),
            'deploydog'                 => array(
                'name' => esc_html__( 'deploy.dog', '@@text_domain' ),
                'kays' => array( 'dd' ),
            ),
            'deskpro'                   => esc_html__( 'Deskpro', '@@text_domain' ),
            'dev'                       => esc_html__( 'Dev', '@@text_domain' ),
            'deviantart'                => esc_html__( 'DeviantArt', '@@text_domain' ),
            'dhl'                       => esc_html__( 'DHL', '@@text_domain' ),
            'diaspora'                  => esc_html__( 'Diaspora', '@@text_domain' ),
            'digg'                      => esc_html__( 'Digg', '@@text_domain' ),
            'digital-ocean'             => esc_html__( 'Digital Ocean', '@@text_domain' ),
            'discord'                   => esc_html__( 'Discord', '@@text_domain' ),
            'discourse'                 => esc_html__( 'Discourse', '@@text_domain' ),
            'dochub'                    => esc_html__( 'DocHub', '@@text_domain' ),
            'docker'                    => esc_html__( 'Docker', '@@text_domain' ),
            'draft2digital'             => esc_html__( 'Draft2Digital', '@@text_domain' ),
            'dribbble'                  => esc_html__( 'Dribbble', '@@text_domain' ),
            'dropbox'                   => esc_html__( 'Dropbox', '@@text_domain' ),
            'drupal'                    => esc_html__( 'Drupal', '@@text_domain' ),
            'dyalog'                    => esc_html__( 'Dyalog', '@@text_domain' ),
            'earlybirds'                => esc_html__( 'Earlybirds', '@@text_domain' ),
            'ebay'                      => esc_html__( 'eBay', '@@text_domain' ),
            'edge'                      => esc_html__( 'Edge', '@@text_domain' ),
            'elementor'                 => esc_html__( 'Elementor', '@@text_domain' ),
            'ello'                      => esc_html__( 'Ello', '@@text_domain' ),
            'ember'                     => esc_html__( 'Ember', '@@text_domain' ),
            'empire'                    => esc_html__( 'Empire', '@@text_domain' ),
            'envira'                    => esc_html__( 'Envira', '@@text_domain' ),
            'erlang'                    => esc_html__( 'Erlang', '@@text_domain' ),
            'ethereum'                  => esc_html__( 'Ethereum', '@@text_domain' ),
            'etsy'                      => esc_html__( 'Etsy', '@@text_domain' ),
            'evernote'                  => esc_html__( 'Evernote', '@@text_domain' ),
            'expeditedssl'              => esc_html__( 'ExpeditedSSL', '@@text_domain' ),
            'facebook-messenger'        => esc_html__( 'Facebook Messenger', '@@text_domain' ),
            'facebook'                  => esc_html__( 'Facebook', '@@text_domain' ),
            'fantasy-flight-games'      => esc_html__( 'Fantasy Flight Games', '@@text_domain' ),
            'fedex'                     => esc_html__( 'FedEx', '@@text_domain' ),
            'fedora'                    => esc_html__( 'Fedora', '@@text_domain' ),
            'figma'                     => esc_html__( 'Figma', '@@text_domain' ),
            'firefox-browser'           => esc_html__( 'Firefox Browser', '@@text_domain' ),
            'firefox'                   => esc_html__( 'Firefox', '@@text_domain' ),
            'first-order'               => esc_html__( 'First Order', '@@text_domain' ),
            'firstdraft'                => esc_html__( 'Firstdraft', '@@text_domain' ),
            'flickr'                    => esc_html__( 'Flickr', '@@text_domain' ),
            'flipboard'                 => esc_html__( 'Flipboard', '@@text_domain' ),
            'fly'                       => esc_html__( 'Fly', '@@text_domain' ),
            'font-awesome'              => esc_html__( 'Font Awesome', '@@text_domain' ),
            'fonticons'                 => esc_html__( 'Fonticons', '@@text_domain' ),
            'fort-awesome'              => esc_html__( 'Fort Awesome', '@@text_domain' ),
            'forumbee'                  => esc_html__( 'Forumbee', '@@text_domain' ),
            'foursquare'                => esc_html__( 'Foursquare', '@@text_domain' ),
            'free-code-camp'            => esc_html__( 'freeCodeCamp', '@@text_domain' ),
            'freebsd'                   => esc_html__( 'FreeBSD', '@@text_domain' ),
            'fulcrum'                   => esc_html__( 'Fulcrum', '@@text_domain' ),
            'galactic-republic'         => esc_html__( 'Galactic Republic', '@@text_domain' ),
            'galactic-senate'           => esc_html__( 'Galactic Senate', '@@text_domain' ),
            'get-pocket'                => array(
                'name' => esc_html__( 'Pocket', '@@text_domain' ),
                'keys' => array( 'pocket' ),
            ),
            'gg'                        => esc_html__( 'GG', '@@text_domain' ),
            'git'                       => esc_html__( 'Git', '@@text_domain' ),
            'github'                    => esc_html__( 'GitHub', '@@text_domain' ),
            'gitkraken'                 => esc_html__( 'GitKraken', '@@text_domain' ),
            'gitlab'                    => esc_html__( 'GitLab', '@@text_domain' ),
            'gitter'                    => esc_html__( 'Gitter', '@@text_domain' ),
            'glide'                     => esc_html__( 'Glide', '@@text_domain' ),
            'gofore'                    => esc_html__( 'Gofore', '@@text_domain' ),
            'golang'                    => esc_html__( 'Go (programming language)', '@@text_domain' ),
            'goodreads'                 => esc_html__( 'Goodreads', '@@text_domain' ),
            'google-drive'              => esc_html__( 'Google Drive', '@@text_domain' ),
            'google-pay'                => esc_html__( 'Google Pay', '@@text_domain' ),
            'google-play'               => esc_html__( 'Google Play', '@@text_domain' ),
            'google-plus'               => esc_html__( 'Google Plus', '@@text_domain' ),
            'google-scholar'            => esc_html__( 'Google Scholar', '@@text_domain' ),
            'google-wallet'             => esc_html__( 'Google Wallet', '@@text_domain' ),
            'google'                    => esc_html__( 'Google', '@@text_domain' ),
            'gratipay'                  => esc_html__( 'Gratipay', '@@text_domain' ),
            'grav'                      => esc_html__( 'Grav', '@@text_domain' ),
            'gripfire'                  => esc_html__( 'Gripfire', '@@text_domain' ),
            'grunt'                     => esc_html__( 'Grunt', '@@text_domain' ),
            'guilded'                   => esc_html__( 'Guilded', '@@text_domain' ),
            'gulp'                      => esc_html__( 'Gulp', '@@text_domain' ),
            'hacker-news'               => esc_html__( 'Hacker News', '@@text_domain' ),
            'hackerrank'                => esc_html__( 'HackerRank', '@@text_domain' ),
            'hashnode'                  => esc_html__( 'Hashnode', '@@text_domain' ),
            'hips'                      => esc_html__( 'HIPS', '@@text_domain' ),
            'hire-a-helper'             => esc_html__( 'HireAHelper', '@@text_domain' ),
            'hive'                      => esc_html__( 'Hive', '@@text_domain' ),
            'hornbill'                  => esc_html__( 'Hornbill', '@@text_domain' ),
            'hotjar'                    => esc_html__( 'Hotjar', '@@text_domain' ),
            'houzz'                     => esc_html__( 'Houzz', '@@text_domain' ),
            'html5'                     => esc_html__( 'HTML5', '@@text_domain' ),
            'hubspot'                   => esc_html__( 'HubSpot', '@@text_domain' ),
            'ideal'                     => esc_html__( 'iDEAL', '@@text_domain' ),
            'imdb'                      => esc_html__( 'IMDb', '@@text_domain' ),
            'instagram'                 => esc_html__( 'Instagram', '@@text_domain' ),
            'instalod'                  => esc_html__( 'InstaLOD', '@@text_domain' ),
            'intercom'                  => esc_html__( 'Intercom', '@@text_domain' ),
            'internet-explorer'         => array(
                'name' => esc_html__( 'Internet Explorer', '@@text_domain' ),
                'keys' => array( 'ie' ),
            ),
            'invision'                  => esc_html__( 'InVision', '@@text_domain' ),
            'ioxhost'                   => esc_html__( 'IoxHost', '@@text_domain' ),
            'itch-io'                   => esc_html__( 'itch.io', '@@text_domain' ),
            'itunes'                    => esc_html__( 'iTunes', '@@text_domain' ),
            'java'                      => esc_html__( 'Java', '@@text_domain' ),
            'jedi-order'                => esc_html__( 'Jedi Order', '@@text_domain' ),
            'jenkins'                   => esc_html__( 'Jenkins', '@@text_domain' ),
            'jira'                      => esc_html__( 'Jira', '@@text_domain' ),
            'joget'                     => esc_html__( 'Joget', '@@text_domain' ),
            'joomla'                    => esc_html__( 'Joomla', '@@text_domain' ),
            'js'                        => array(
                'name' => esc_html__( 'JS', '@@text_domain' ),
                'keys' => array( 'javascript' ),
            ),
            'jsfiddle'                  => esc_html__( 'JSFiddle', '@@text_domain' ),
            'kaggle'                    => esc_html__( 'Kaggle', '@@text_domain' ),
            'keybase'                   => esc_html__( 'Keybase', '@@text_domain' ),
            'keycdn'                    => esc_html__( 'KeyCDN', '@@text_domain' ),
            'kickstarter'               => esc_html__( 'Kickstarter', '@@text_domain' ),
            'korvue'                    => esc_html__( 'Korvue', '@@text_domain' ),
            'laravel'                   => esc_html__( 'Laravel', '@@text_domain' ),
            'lastfm'                    => esc_html__( 'Last.fm', '@@text_domain' ),
            'leanpub'                   => esc_html__( 'Leanpub', '@@text_domain' ),
            'less'                      => esc_html__( 'Less', '@@text_domain' ),
            'letterboxd'                => esc_html__( 'Letterboxd', '@@text_domain' ),
            'line'                      => esc_html__( 'Line', '@@text_domain' ),
            'linkedin'                  => esc_html__( 'LinkedIn', '@@text_domain' ),
            'linode'                    => esc_html__( 'Linode', '@@text_domain' ),
            'linux'                     => esc_html__( 'Linux', '@@text_domain' ),
            'lyft'                      => esc_html__( 'Lyft', '@@text_domain' ),
            'magento'                   => esc_html__( 'Magento', '@@text_domain' ),
            'mailchimp'                 => esc_html__( 'Mailchimp', '@@text_domain' ),
            'mandalorian'               => esc_html__( 'Mandalorian', '@@text_domain' ),
            'markdown'                  => array(
                'name' => esc_html__( 'Markdown', '@@text_domain' ),
                'keys' => array( 'md' ),
            ),
            'mastodon'                  => esc_html__( 'Mastodon', '@@text_domain' ),
            'maxcdn'                    => esc_html__( 'MaxCDN', '@@text_domain' ),
            'mdb'                       => esc_html__( 'MDB', '@@text_domain' ),
            'medapps'                   => esc_html__( 'MedApps', '@@text_domain' ),
            'medium'                    => esc_html__( 'Medium', '@@text_domain' ),
            'medrt'                     => esc_html__( 'Medrt', '@@text_domain' ),
            'meetup'                    => esc_html__( 'Meetup', '@@text_domain' ),
            'megaport'                  => esc_html__( 'Megaport', '@@text_domain' ),
            'mendeley'                  => esc_html__( 'Mendeley', '@@text_domain' ),
            'meta'                      => esc_html__( 'Meta', '@@text_domain' ),
            'microblog'                 => esc_html__( 'Micro.blog', '@@text_domain' ),
            'microsoft'                 => esc_html__( 'Microsoft', '@@text_domain' ),
            'mintbit'                   => esc_html__( 'Mintbit', '@@text_domain' ),
            'mix'                       => esc_html__( 'Mix', '@@text_domain' ),
            'mixcloud'                  => esc_html__( 'Mixcloud', '@@text_domain' ),
            'mixer'                     => esc_html__( 'Mixer', '@@text_domain' ),
            'mizuni'                    => esc_html__( 'Mizuni', '@@text_domain' ),
            'modx'                      => esc_html__( 'MODX', '@@text_domain' ),
            'monero'                    => esc_html__( 'Monero', '@@text_domain' ),
            'napster'                   => esc_html__( 'Mapster', '@@text_domain' ),
            'neos'                      => esc_html__( 'Neos', '@@text_domain' ),
            'nimblr'                    => esc_html__( 'Nimblr', '@@text_domain' ),
            'node-js'                   => esc_html__( 'Node.js', '@@text_domain' ),
            'node'                      => esc_html__( 'Node', '@@text_domain' ),
            'npm'                       => esc_html__( 'npm', '@@text_domain' ),
            'ns8'                       => esc_html__( 'NS8', '@@text_domain' ),
            'nutritionix'               => esc_html__( 'Nutritionix', '@@text_domain' ),
            'octopus-deploy'            => esc_html__( 'Octopus Deploy', '@@text_domain' ),
            'odnoklassniki'             => array(
                'name' => esc_html__( 'Odnoklassniki', '@@text_domain' ),
                'keys' => array( 'ok' ),
            ),
            'odysee'                    => esc_html__( 'Odysee', '@@text_domain' ),
            'old-republic'              => esc_html__( 'Old Republic', '@@text_domain' ),
            'opencart'                  => esc_html__( 'OpenCart', '@@text_domain' ),
            'openid'                    => esc_html__( 'OpenID', '@@text_domain' ),
            'opensuse'                  => esc_html__( 'openSUSE', '@@text_domain' ),
            'opera'                     => esc_html__( 'Opera', '@@text_domain' ),
            'optin-monster'             => esc_html__( 'OptinMonster', '@@text_domain' ),
            'orcid'                     => esc_html__( 'ORCID', '@@text_domain' ),
            'osi'                       => esc_html__( 'OSI', '@@text_domain' ),
            'padlet'                    => esc_html__( 'Padlet', '@@text_domain' ),
            'page4'                     => esc_html__( 'PAGE4', '@@text_domain' ),
            'pagelines'                 => esc_html__( 'PageLines', '@@text_domain' ),
            'palfed'                    => esc_html__( 'PalFed', '@@text_domain' ),
            'patreon'                   => esc_html__( 'Patreon', '@@text_domain' ),
            'paypal'                    => esc_html__( 'PayPal', '@@text_domain' ),
            'penny-arcade'              => esc_html__( 'Penny Arcade', '@@text_domain' ),
            'perbyte'                   => esc_html__( 'PerByte', '@@text_domain' ),
            'periscope'                 => esc_html__( 'Periscope', '@@text_domain' ),
            'phabricator'               => esc_html__( 'Phabricator', '@@text_domain' ),
            'phoenix-framework'         => esc_html__( 'Phoenix Framework', '@@text_domain' ),
            'phoenix-squadron'          => esc_html__( 'Phoenix Squadron', '@@text_domain' ),
            'php'                       => esc_html__( 'PHP', '@@text_domain' ),
            'pinterest'                 => esc_html__( 'Pinterest', '@@text_domain' ),
            'pix'                       => esc_html__( 'PIX', '@@text_domain' ),
            'pixiv'                     => esc_html__( 'Pixiv', '@@text_domain' ),
            'playstation'               => array(
                'name' => esc_html__( 'PlayStation', '@@text_domain' ),
                'keys' => array( 'ps' ),
            ),
            'product-hunt'              => esc_html__( 'Product Hunt', '@@text_domain' ),
            'pushed'                    => esc_html__( 'Pushed', '@@text_domain' ),
            'python'                    => esc_html__( 'Python', '@@text_domain' ),
            'qq'                        => array(
                'name' => esc_html__( 'Tencent QQ', '@@text_domain' ),
                'keys' => array( 'tencent-qq' ),
            ),
            'quinscape'                 => esc_html__( 'QuinScape', '@@text_domain' ),
            'quora'                     => esc_html__( 'Quora', '@@text_domain' ),
            'r-project'                 => esc_html__( 'R', '@@text_domain' ),
            'raspberry-pi'              => esc_html__( 'Raspberry Pi', '@@text_domain' ),
            'ravelry'                   => esc_html__( 'Ravelry', '@@text_domain' ),
            'react'                     => esc_html__( 'React', '@@text_domain' ),
            'reacteurope'               => esc_html__( 'ReactEurope', '@@text_domain' ),
            'readme'                    => esc_html__( 'ReadMe', '@@text_domain' ),
            'rebel'                     => esc_html__( 'Rebel', '@@text_domain' ),
            'red-river'                 => esc_html__( 'Red River', '@@text_domain' ),
            'reddit'                    => esc_html__( 'reddit', '@@text_domain' ),
            'redhat'                    => esc_html__( 'Red Hat', '@@text_domain' ),
            'renren'                    => esc_html__( 'Renren', '@@text_domain' ),
            'replyd'                    => esc_html__( 'Replyd', '@@text_domain' ),
            'researchgate'              => esc_html__( 'ResearchGate', '@@text_domain' ),
            'resolving'                 => esc_html__( 'Resolving', '@@text_domain' ),
            'rev'                       => esc_html__( 'Rev', '@@text_domain' ),
            'rocketchat'                => esc_html__( 'Rocket.Chat', '@@text_domain' ),
            'rockrms'                   => esc_html__( 'Rock RMS', '@@text_domain' ),
            'rust'                      => esc_html__( 'Rust', '@@text_domain' ),
            'safari'                    => esc_html__( 'Safari', '@@text_domain' ),
            'salesforce'                => esc_html__( 'Salesforce', '@@text_domain' ),
            'sass'                      => esc_html__( 'Sass', '@@text_domain' ),
            'schlix'                    => esc_html__( 'SCHLIX', '@@text_domain' ),
            'scribd'                    => esc_html__( 'Scribd', '@@text_domain' ),
            'screenpal'                 => esc_html__( 'ScreenPal', '@@text_domain' ),
            'searchengin'               => esc_html__( 'Searchengin', '@@text_domain' ),
            'sellcast'                  => esc_html__( 'SellCast', '@@text_domain' ),
            'sellsy'                    => esc_html__( 'Sellsy', '@@text_domain' ),
            'servicestack'              => esc_html__( 'ServiceStack', '@@text_domain' ),
            'shirtsinbulk'              => esc_html__( 'Shirts In Bulk', '@@text_domain' ),
            'shopify'                   => esc_html__( 'Shopify', '@@text_domain' ),
            'shopware'                  => esc_html__( 'Shopware', '@@text_domain' ),
            'simplybuilt'               => esc_html__( 'SimplyBuilt', '@@text_domain' ),
            'sistrix'                   => esc_html__( 'SISTRIX', '@@text_domain' ),
            'sith'                      => esc_html__( 'Sith', '@@text_domain' ),
            'sitrox'                    => esc_html__( 'Sitrox', '@@text_domain' ),
            'sketch'                    => esc_html__( 'Sketch', '@@text_domain' ),
            'skyatlas'                  => esc_html__( 'SkyAtlas', '@@text_domain' ),
            'skype'                     => esc_html__( 'Skype', '@@text_domain' ),
            'slack'                     => esc_html__( 'Slack', '@@text_domain' ),
            'slideshare'                => esc_html__( 'SlideShare', '@@text_domain' ),
            'snapchat'                  => esc_html__( 'Snapchat', '@@text_domain' ),
            'soundcloud'                => esc_html__( 'SoundCloud', '@@text_domain' ),
            'sourcetree'                => esc_html__( 'Sourcetree', '@@text_domain' ),
            'speakap'                   => esc_html__( 'Speakap', '@@text_domain' ),
            'speaker-deck'              => esc_html__( 'Speaker Deck', '@@text_domain' ),
            'spotify'                   => esc_html__( 'Spotify', '@@text_domain' ),
            'squarespace'               => esc_html__( 'Squarespace', '@@text_domain' ),
            'stack-exchange'            => esc_html__( 'Stack Exchange', '@@text_domain' ),
            'stack-overflow'            => esc_html__( 'Stack Overflow', '@@text_domain' ),
            'stackpath'                 => esc_html__( 'StackPath', '@@text_domain' ),
            'staylinked'                => esc_html__( 'StayLinked', '@@text_domain' ),
            'steam'                     => esc_html__( 'Steam', '@@text_domain' ),
            'sticker-mule'              => esc_html__( 'Sticker Mule', '@@text_domain' ),
            'strava'                    => esc_html__( 'Strava', '@@text_domain' ),
            'stripe'                    => esc_html__( 'Stripe', '@@text_domain' ),
            'stubber'                   => esc_html__( 'Stubber', '@@text_domain' ),
            'studiovinari'              => esc_html__( 'Studio Vinari', '@@text_domain' ),
            'stumbleupon'               => esc_html__( 'StumbleUpon', '@@text_domain' ),
            'superpowers'               => esc_html__( 'Superpowers', '@@text_domain' ),
            'supple'                    => esc_html__( 'Supple', '@@text_domain' ),
            'suse'                      => esc_html__( 'SuSE', '@@text_domain' ),
            'swift'                     => esc_html__( 'Swift', '@@text_domain' ),
            'symfony'                   => esc_html__( 'Symfony', '@@text_domain' ),
            'teamspeak'                 => esc_html__( 'SeamSpeak', '@@text_domain' ),
            'telegram'                  => esc_html__( 'Telegram', '@@text_domain' ),
            'tencent-weibo'             => esc_html__( 'Tencent Weibo', '@@text_domain' ),
            'the-red-yeti'              => esc_html__( 'The Red Yeti', '@@text_domain' ),
            'themeisle'                 => esc_html__( 'Themeisle', '@@text_domain' ),
            'threads'                   => esc_html__( 'Threads', '@@text_domain' ),
            'think-peaks'               => esc_html__( 'ThinkPeaks', '@@text_domain' ),
            'tiktok'                    => esc_html__( 'TikTok', '@@text_domain' ),
            'trade-federation'          => esc_html__( 'Trade Federation', '@@text_domain' ),
            'trello'                    => esc_html__( 'Trello', '@@text_domain' ),
            'tripadvisor'               => esc_html__( 'Tripadvisor', '@@text_domain' ),
            'tumblr'                    => esc_html__( 'Tumblr', '@@text_domain' ),
            'twitch'                    => esc_html__( 'Twitch', '@@text_domain' ),
            'twitter'                   => esc_html__( 'Twitter', '@@text_domain' ),
            'typo3'                     => esc_html__( 'TYPO3', '@@text_domain' ),
            'uber'                      => esc_html__( 'Uber', '@@text_domain' ),
            'ubuntu'                    => esc_html__( 'Ubuntu', '@@text_domain' ),
            'uikit'                     => esc_html__( 'UIkit', '@@text_domain' ),
            'umbraco'                   => esc_html__( 'Umbraco', '@@text_domain' ),
            'uncharted'                 => esc_html__( 'Uncharted', '@@text_domain' ),
            'uniregistry'               => esc_html__( 'Uniregistry', '@@text_domain' ),
            'unity'                     => esc_html__( 'Unity', '@@text_domain' ),
            'unsplash'                  => esc_html__( 'Unsplash', '@@text_domain' ),
            'untappd'                   => esc_html__( 'Untappd', '@@text_domain' ),
            'ups'                       => esc_html__( 'UPS', '@@text_domain' ),
            'upwork'                    => esc_html__( 'Upwork', '@@text_domain' ),
            'usps'                      => esc_html__( 'USPS', '@@text_domain' ),
            'ussunnah'                  => esc_html__( 'us-Sunnah', '@@text_domain' ),
            'vaadin'                    => esc_html__( 'Vaadin', '@@text_domain' ),
            'viacoin'                   => esc_html__( 'Viacoin', '@@text_domain' ),
            'viadeo'                    => esc_html__( 'Viadeo', '@@text_domain' ),
            'viber'                     => esc_html__( 'Viber', '@@text_domain' ),
            'vimeo'                     => esc_html__( 'Vimeo', '@@text_domain' ),
            'vine'                      => esc_html__( 'Vine', '@@text_domain' ),
            'vk'                        => array(
                'name' => esc_html__( 'VK', '@@text_domain' ),
                'keys' => array( 'vkontakte' ),
            ),
            'vnv'                       => esc_html__( 'VNV', '@@text_domain' ),
            'vuejs'                     => esc_html__( 'Vue.js', '@@text_domain' ),
            'watchman-monitoring'       => esc_html__( 'Watchman Monitoring', '@@text_domain' ),
            'waze'                      => esc_html__( 'Waze', '@@text_domain' ),
            'wechat'                    => array(
                'name' => esc_html__( 'WeChat', '@@text_domain' ),
                'keys' => array( 'weixin' ),
            ),
            'weebly'                    => esc_html__( 'Weebly', '@@text_domain' ),
            'weibo'                     => array(
                'name' => esc_html__( 'Sina Weibo', '@@text_domain' ),
                'keys' => array( 'sina-weibo' ),
            ),
            'weixin'                    => esc_html__( 'Weixin', '@@text_domain' ),
            'whatsapp'                  => esc_html__( 'WhatsApp', '@@text_domain' ),
            'whmcs'                     => esc_html__( 'WHMCS', '@@text_domain' ),
            'wikipedia'                 => esc_html__( 'Wikipedia', '@@text_domain' ),
            'windows'                   => esc_html__( 'Windows', '@@text_domain' ),
            'wix'                       => esc_html__( 'WIX', '@@text_domain' ),
            'wirsindhandwerk'           => esc_html__( 'wirsindhandwerk', '@@text_domain' ),
            'wizards-of-the-coast'      => esc_html__( 'Wizards of the Coast', '@@text_domain' ),
            'wodu'                      => esc_html__( 'Wodu.', '@@text_domain' ),
            'wolf-pack-battalion'       => esc_html__( 'Wolf Pack Battalion', '@@text_domain' ),
            'wordpress'                 => esc_html__( 'WordPress', '@@text_domain' ),
            'wpbeginner'                => esc_html__( 'WPBeginner', '@@text_domain' ),
            'wpexplorer'                => esc_html__( 'WPExplorer', '@@text_domain' ),
            'wpforms'                   => esc_html__( 'WPForms', '@@text_domain' ),
            'wpressr'                   => esc_html__( 'WPressr', '@@text_domain' ),
            'xbox'                      => esc_html__( 'Xbox', '@@text_domain' ),
            'xing'                      => esc_html__( 'XING', '@@text_domain' ),
            'x-twitter'                 => array(
                'name' => esc_html__( 'X / Twitter', '@@text_domain' ),
                'keys' => array( 'x', 'twitter' ),
            ),
            'y-combinator'              => esc_html__( 'YCombinator', '@@text_domain' ),
            'yahoo'                     => esc_html__( 'Yahoo', '@@text_domain' ),
            'yammer'                    => esc_html__( 'Yammer', '@@text_domain' ),
            'yandex'                    => esc_html__( 'Yandex', '@@text_domain' ),
            'yandex-international'      => esc_html__( 'Yandex International', '@@text_domain' ),
            'yarn'                      => esc_html__( 'Yarn', '@@text_domain' ),
            'yelp'                      => esc_html__( 'Yelp', '@@text_domain' ),
            'yoast'                     => esc_html__( 'Yoast', '@@text_domain' ),
            'youtube'                   => esc_html__( 'Youtube', '@@text_domain' ),
            'zhihu'                     => esc_html__( 'Zhihu', '@@text_domain' ),
        );

        /**
         * Custom icons support.
         * Example of new icon placed in your theme:
            array(
                'my_icon' => array(
                    'name'     => 'My Icon',
                    'svg_path' => get_template_directory() . '/icons/my-icon.svg',
                ),
            );
         */
        $custom_brands = apply_filters( 'ghost_framework_custom_brand_icons', array() );

        $brands    = array_merge( $brands, $custom_brands );
        $result    = array();
        $base_path = __DIR__ . '/svg/';

        // Prepare SVG paths.
        foreach ( $brands as $k => $data ) {
            $svg_path = isset( $data['svg_path'] ) ? $data['svg_path'] : ( $base_path . $k . '.svg' );

            if ( file_exists( $svg_path ) ) {
                $result[ $k ] = array_merge(
                    is_array( $data ) ? $data : array(
                        'name' => $data,
                    ),
                    $get_svg ? array(
                        'svg' => self::get_svg_by_path( $svg_path, $svg_data ),
                    ) : array(),
                    array(
                        'svg_path' => $svg_path,
                    )
                );
            }
        }

        // Sort by key.
        ksort( $result );

        return $result;
    }
}
