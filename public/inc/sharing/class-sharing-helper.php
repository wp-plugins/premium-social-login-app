<?php

// Exit if called directly
if ( !defined( 'ABSPATH' ) ) {
    exit();
}

if ( !class_exists( 'SharingLA_Helper' ) ) {

    /**
     * Helper/Utility class for social sharing functionality
     */
    class SharingLA_Helper {

        /**
         * Add LoginApp Plugin Sharing script pn pages and posts.
         *
         * global $loginAppSettings;
         */
        public static function login_app_sharing_get_sharing_script() {
            global $loginAppSettings;
            $sharingScript = '<script type="text/javascript">var islrsharing = true; var islrsocialcounter = true; var hybridsharing = true;</script> <script type="text/javascript" src="//share.loginradius.com/Content/js/LoginRadius.js" id="lrsharescript"></script>';
            $sharingScript .= '<script type="text/javascript">';

            if ( $loginAppSettings['horizontal_shareEnable'] == '1' ) {
                // check horizontal sharing enabled
                $sharingScript .= self:: login_app_sharing_get_sharing_script_horizontal( $loginAppSettings );
            }
            if ( $loginAppSettings['vertical_shareEnable'] == '1' ) {
                // check vertical sharing enabled
                $sharingScript .= self::login_app_sharing_get_sharing_script_vertical( $loginAppSettings );
            }

            $sharingScript .= '</script>';
            echo $sharingScript;
        }

        /**
         * function returns script required for horizontal sharing.
         *
         * global $loginAppSettings;
         */
        public static function login_app_sharing_get_sharing_script_horizontal() {
            global $loginAppSettings;
            $size = '';
            $interface = '';
            $sharingScript = '';
            $horizontalThemvalue = isset( $loginAppSettings['horizontalSharing_theme'] ) ? $loginAppSettings['horizontalSharing_theme'] : '';

            switch ( $horizontalThemvalue ) {
                case '32':
                    $size = '32';
                    $interface = 'horizontal';
                    break;

                case '16':
                    $size = '16';
                    $interface = 'horizontal';
                    break;

                case 'single_large':
                    $size = '32';
                    $interface = 'simpleimage';
                    break;

                case 'single_small':
                    $size = '16';
                    $interface = 'simpleimage';
                    break;

                case 'counter_vertical':
                    $ishorizontal = 'true';
                    $interface = 'simple';
                    $countertype = 'vertical';
                    break;

                case 'counter_horizontal':
                    $ishorizontal = 'true';
                    $interface = 'simple';
                    $countertype = 'horizontal';
                    break;

                default:
                    $size = '32';
                    $interface = 'horizontal';
                    break;
            }
            if ( !empty( $ishorizontal ) ) {
                $providers = self:: get_counter_providers( 'horizontal' );
                // prepare counter script
                $sharingScript .= 'LoginRadius.util.ready( function() { $SC.Providers.Selected = ["' . $providers . '"]; $S = $SC.Interface.' . $interface . '; $S.isHorizontal = ' . $ishorizontal . '; $S.countertype = \'' . $countertype . '\'; $u = LoginRadius.user_settings; $u.isMobileFriendly = true; $S.show( "loginRadiusHorizontalSharing" ); } );';
            } else {
                $providers = self:: get_sharing_providers( 'horizontal' );
                // prepare sharing script
                $sharingScript .= 'LoginRadius.util.ready( function() { $i = $SS.Interface.' . $interface . '; $SS.Providers.Top = ["' . $providers . '"]; $u = LoginRadius.user_settings;';
                if ( isset( $loginAppSettings['LoginApp_apikey'] ) && !empty( $loginAppSettings['LoginApp_apikey'] ) ) {
                    $sharingScript .= '$u.apikey= \'' . trim( $loginAppSettings['LoginApp_apikey'] ) . '\';';
                }
                $sharingScript .= '$i.size = ' . $size . '; $u.sharecounttype="url"; $u.isMobileFriendly=true; $i.show( "loginRadiusHorizontalSharing" ); } );';
            }
            return $sharingScript;
        }

        /**
         * function returns script required for vertical sharing.
         *
         * global $loginAppSettings;
         */
        public static function login_app_sharing_get_sharing_script_vertical() {
            global $loginAppSettings;
            $sharingScript = '';
            $verticalThemvalue = isset( $loginAppSettings['verticalSharing_theme'] ) ? $loginAppSettings['verticalSharing_theme'] : '';

            switch ( $verticalThemvalue ) {
                case '32':
                    $size = '32';
                    $interface = 'Simplefloat';
                    $sharingVariable = 'i';
                    break;

                case '16':
                    $size = '16';
                    $interface = 'Simplefloat';
                    $sharingVariable = 'i';
                    break;

                case 'counter_vertical':
                    $sharingVariable = 'S';
                    $ishorizontal = 'false';
                    $interface = 'simple';
                    $type = 'vertical';
                    break;

                case 'counter_horizontal':
                    $sharingVariable = 'S';
                    $ishorizontal = 'false';
                    $interface = 'simple';
                    $type = 'horizontal';
                    break;

                default:
                    $size = '32';
                    $interface = 'Simplefloat';
                    $sharingVariable = 'i';
                    break;
            }

            $verticalPosition = isset( $loginAppSettings['sharing_verticalPosition'] ) ? $loginAppSettings['sharing_verticalPosition'] : '';
            switch ( $verticalPosition ) {
                case "top_left":
                    $position1 = 'top';
                    $position2 = 'left';
                    break;

                case "top_right":
                    $position1 = 'top';
                    $position2 = 'right';
                    break;

                case "bottom_left":
                    $position1 = 'bottom';
                    $position2 = 'left';
                    break;

                case "bottom_right":
                    $position1 = 'bottom';
                    $position2 = 'right';
                    break;

                default:
                    $position1 = 'top';
                    $position2 = 'left';
                    break;
            }

            $offset = '$' . $sharingVariable . '.' . $position1 . ' = \'0px\'; $' . $sharingVariable . '.' . $position2 . ' = \'0px\';';

            if ( empty( $size ) ) {
                $providers = self:: get_counter_providers( 'vertical' );
                $sharingScript .= 'LoginRadius.util.ready( function() { $SC.Providers.Selected = ["' . $providers . '"]; $S = $SC.Interface.' . $interface . '; $S.isHorizontal = ' . $ishorizontal . '; $S.countertype = \'' . $type . '\'; ' . $offset . ' $u = LoginRadius.user_settings; $u.isMobileFriendly = true; $S.show( "loginRadiusVerticalSharing" ); } );';
            } else {
                $providers = self:: get_sharing_providers( 'vertical' );
                // prepare sharing script
                $sharingScript .= 'LoginRadius.util.ready( function() { $i = $SS.Interface.' . $interface . '; $SS.Providers.Top = ["' . $providers . '"]; $u = LoginRadius.user_settings;';
                $sharingScript .= '$u.apikey= \'' . trim( $loginAppSettings['LoginApp_apikey'] ) . '\';';
                $sharingScript .= '$i.size = ' . $size . '; ' . $offset . ' $u.isMobileFriendly=true; $i.show( "loginRadiusVerticalSharing" ); } );';
            }
            return $sharingScript;
        }

        /**
         * function returns comma seperated counters lists
         *
         * global $loginAppSettings;
         */
        public static function get_counter_providers( $themeType ) {
            global $loginAppSettings;
            $searchOption = $themeType . '_counter_providers';
            if ( isset( $loginAppSettings[$searchOption] ) && is_array( $loginAppSettings[$searchOption] ) && count( $loginAppSettings[$searchOption] ) > 0 ) {
                return implode( '","', $loginAppSettings[$searchOption] );
            } else {
                return 'Facebook Like","Google+ +1","Pinterest Pin it","LinkedIn Share","Hybridshare';
            }
        }

        /**
         * function returns comma seperated sharing providers lists
         *
         * global $loginAppSettings;
         */
        public static function get_sharing_providers( $themeType ) {
            global $loginAppSettings;
            $searchOption = $themeType . '_rearrange_providers';
            if ( isset( $loginAppSettings[$searchOption] ) && is_array( $loginAppSettings[$searchOption] ) && count( $loginAppSettings[$searchOption] ) > 0 ) {
                return implode( '","', $loginAppSettings[$searchOption] );
            } else {
                return 'Facebook","Twitter","Pinterest","Print","Email';
            }
        }

        /**
         * Callback for filter the_content,
         * This function insert appropriate div for Sharing on WordPress pages/posts
         */
        public static function login_app_sharing_content( $content ) {

            global $post, $loginAppSettings;
            $lrMeta = get_post_meta( $post->ID, '_login_app_meta', true );

            // if sharing disabled on this page/post, return content unaltered
            if ( isset( $lrMeta['sharing'] ) && $lrMeta['sharing'] == 1 && !is_front_page() ) {
                return $content;
            }
            if ( isset( $loginAppSettings['horizontal_shareEnable'] ) && $loginAppSettings['horizontal_shareEnable'] == '1' ) {
                // If horizontal sharing is enabled
                $loginRadiusHorizontalSharingDiv = '<div class="loginRadiusHorizontalSharing"';
                $loginRadiusHorizontalSharingDiv .= ' data-share-url="' . get_permalink( $post->ID ) . '" data-counter-url="' . get_permalink( $post->ID ) . '"';
                $loginRadiusHorizontalSharingDiv .= ' ></div>';

                $horizontalDiv = $loginRadiusHorizontalSharingDiv;
                $sharingFlag = '';
                //displaying sharing interface on home page
                if ( ( ( isset( $loginAppSettings['horizontal_sharehome'] ) && current_filter() == 'the_content' ) || ( isset( $loginAppSettings['horizontal_shareexcerpt'] ) && current_filter() == 'get_the_excerpt' ) ) && is_front_page() && isset( $loginAppSettings['horizontal_sharehome'] ) ) {
                    //checking if current page is home page and sharing on home page is enabled.
                    $sharingFlag = 'true';
                }
                //displaying sharing interface on Post and pages
                if ( ( isset( $loginAppSettings['horizontal_sharepost'] ) && is_single() ) || ( isset( $loginAppSettings['horizontal_sharepage'] ) && is_page() && !is_front_page()) ) {
                    $sharingFlag = 'true';
                }

                if ( ( isset( $loginAppSettings['horizontal_sharepost'] ) && current_filter() == 'the_content' && is_single() ) || ( isset( $loginAppSettings['horizontal_shareexcerpt'] ) && current_filter() == 'get_the_excerpt' && is_page() ) ) {
                    //checking if custom page is used for displaying posts
                    $sharingFlag = 'true';
                }

                if ( is_page() && !is_front_page() && isset( $loginAppSettings['horizontal_sharepage'] ) ) {
                    //If not home page and sharing on pages is enabled.
                    $sharingFlag = 'true';
                }

                if ( is_front_page() && !isset( $loginAppSettings['horizontal_sharehome'] ) ) {
                    //If sharing on front page disabled.
                    if ( true == $sharingFlag ) {
                        $sharingFlag = '';
                    }
                }

                if ( isset( $loginAppSettings['horizontal_shareexcerpt'] ) && current_filter() == 'get_the_excerpt' ) {
                    //If sharing on Post Excerpts is enabled.
                    $sharingFlag = 'true';
                }

                if ( isset( $loginAppSettings['horizontal_sharepost'] ) && current_filter() == 'the_content' && !is_single() && is_home() && isset( $loginAppSettings['horizontal_sharehome'] ) ) {
                    //If sharing on Post  is enabled and page is blog/home page.
                    $sharingFlag = 'true';
                }

                if ( !empty( $sharingFlag ) ) {
                    if ( isset( $loginAppSettings['horizontal_shareTop'] ) && isset( $loginAppSettings['horizontal_shareBottom'] ) ) {
                        $content = $horizontalDiv . '<br/>' . $content . '<br/>' . $horizontalDiv;
                    } else {
                        if ( isset( $loginAppSettings['horizontal_shareTop'] ) ) {
                            $content = $horizontalDiv . $content;
                        } elseif ( isset( $loginAppSettings['horizontal_shareBottom'] ) ) {
                            $content = $content . $horizontalDiv;
                        }
                    }
                }
            }
            if ( isset( $loginAppSettings['vertical_shareEnable'] ) && $loginAppSettings['vertical_shareEnable'] == '1' ) {
                $vertcalSharingFlag = '';
                $loginRadiusVerticalSharingDiv = '<div class="loginRadiusVerticalSharing" style="z-index: 10000000000"></div>';

                if ( ( ( isset( $loginAppSettings['vertical_sharehome'] ) && current_filter() == 'the_content' ) ) && is_front_page() && isset( $loginAppSettings['vertical_sharehome'] ) ) {
                    // If vertical sharing on Home page enabled.
                    $vertcalSharingFlag = 'true';
                }

                if ( ( isset( $loginAppSettings['vertical_sharepost'] ) && current_filter() == 'the_content' ) && is_page() ) {
                    //checking if custom page is used for displaying posts.
                    $vertcalSharingFlag = 'true';
                }

                if ( ( isset( $loginAppSettings['vertical_sharepost'] ) && is_single() ) || ( isset( $loginAppSettings['vertical_sharepage'] ) && is_page() ) ) {
                    //displaying sharing interface on Post and pages.
                    $vertcalSharingFlag = 'true';
                }

                if ( is_page() && !is_front_page() && isset( $loginAppSettings['vertical_sharepage'] ) ) {
                    //If not front page and vertical sharing on pages is enabled.
                    $vertcalSharingFlag = 'true';
                }
                if ( is_front_page() && !isset( $loginAppSettings['vertical_sharehome'] ) ) {
                    //if page is front page and vertical sharing is disabled on home page.
                    if ( $sharingFlag ) {
                        $vertcalSharingFlag = '';
                    }
                }
                if ( is_home() && isset( $loginAppSettings['vertical_sharehome'] ) ) {

                    $vertcalSharingFlag = 'true';
                }
                if ( !empty( $vertcalSharingFlag ) ) {
                    //if Vertical sharing is needed on current page.
                    global $loginRadiusSharingVerticalInterfaceContentCount, $loginRadiusSharingVerticalInterfaceExcerptCount;
                    if ( current_filter() == 'the_content' ) {
                        $compareVariable = 'loginRadiusSharingVerticalInterfaceContentCount';
                    } elseif ( current_filter() == 'get_the_excerpt' ) {
                        $compareVariable = 'loginRadiusSharingVerticalInterfaceExcerptCount';
                    }
                    if ( $$compareVariable == 0 ) {
                        $content = $content . $loginRadiusVerticalSharingDiv;
                        $$compareVariable++;
                    } else {
                        $content = $content . $loginRadiusVerticalSharingDiv;
                    }
                }
            }
            //returnig the content with sharing interface.
            return $content;
        }

    }

}
