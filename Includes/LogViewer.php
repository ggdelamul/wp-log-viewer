<?php
namespace WPLogViewer;
//sécurite du plugin
defined('ABSPATH') || exit; 

class LogViewer
{
    private static $instance = null;
    private $log_file;
    //  singleton : empêche plusieurs instances
    public static function get_instance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    //  initialise le plugin
    public function init() {
        $this->log_file = WP_CONTENT_DIR . '/debug.log';

        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_init', [$this, 'handle_log_clear']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
    }
    //  s'exécute à l'activation du plugin
    public static function activate() {
        if (!get_option('log_viewer_initialized')) {
            add_option('log_viewer_initialized', 'Date d\'activation '. date('Y-m-d H:i:s'));    
        }
       update_option('log_viewer_initialized', 'Date d\'activation '. date('Y-m-d H:i:s'));
       error_log('[Plugin] activé :'. get_option('log_viewer_initialized'));
       //créer une table en bdd

    }
    public static function deactivate() {
        // on ne supprime rien car c'est fait à la désinstallation
        // /déconnecter services externes	
        // réinitialiser certaines configs wp	
        // supprimer fichiers/cache générés	

        error_log('[Plugin] activé :'. get_option('log_viewer_initialized'));
    }
    //  s'exécute à la désinstallation du plugin
    public static function uninstall() {
        // On supprime proprement les options ajoutées
        /*
        supprimer options	
        supprimer tables SQL	
        supprimer CPT	nettoyer les posts avec le cpt 
        supprimer rôles/capacités crée 
        */ 
        delete_option('log_viewer_initialized'); // Nettoyage option
        error_log('[Plugin] Désinstallation : plugin supprimé à ' . date('Y-m-d H:i:s'));
    }
    //ajouter un script JS uniquement lorsque que l'on se trouve sur l'extension
    public function enqueue_admin_assets($hook)
        {
            // cible uniquement la page admin de l'extension
            if ($hook !== 'toplevel_page_debug-log-viewer') {
                return;
            }
            wp_enqueue_style (
                'logviewer-admin-style',
                plugin_dir_url(__FILE__) . '../assets/css/admin-style.css',
                [],
                true
            );
            
                
            
            wp_enqueue_script(
                'logviewer-admin-script',
                plugin_dir_url(__FILE__) . '../assets/js/admin-script.js',
                [],
                true
            );
        }



    // ajouter le menu dans l'administration wp
    public function add_admin_menu() {
    add_menu_page(
        'Fichier de log',         // titre affiché sur la page une fois ouverte
        'Fichier de log',         // texte du menu dans la barre d'administration
        'manage_options',         // droit  pour voir ce menu (admin uniquement)
        'debug-log-viewer',       // slug pour identifier la page (utilisé dans l'url admin.php?page=debug-log-viewer)
        [$this, 'render_log_page'], // fonction callback appelée pour afficher le contenu de la page
        'dashicons-media-text',   // icone du menu (bibliothèque d'icone wp)
        90                        // position du menu dans l'admin (90 = bas de la liste)
    );
    }
    //  gere la demande de vidage du fichier de log
    public function handle_log_clear() {
        //  vérifie si l'url contient l'argument debug_log_clear=1 et si l'utilisateur est administrateur
        if (
            isset($_GET['debug_log_clear']) &&        
            $_GET['debug_log_clear'] === '1' &&        
            current_user_can('manage_options')         
        ) {
            
            if (file_exists($this->log_file)) {
                file_put_contents($this->log_file, ''); // vide complètement le contenu du fichier 
            }

            //  redirection vers la page admin du plugin avec un paramètre cleared=1
            wp_redirect(admin_url('admin.php?page=debug-log-viewer&cleared=1'));

            exit; // renforce l'effet de la redirection
        }
    }
        // affiche la page d'admin du plugin
    public function render_log_page() {
        error_log("pour le script");
        //utilisation de classe wp pour le style de l'admin
        echo '<div class="wrap">';
        echo '<h1>Fichier debug.log</h1>';
        //  affichage message si le log a été vidé (grâce au paramètre url ?cleared=1)
        if (isset($_GET['cleared']) && $_GET['cleared'] == 1) {
            echo '<div class="notice notice-success is-dismissible"><p>Le fichier debug.log a été vidé avec succès.</p></div>';
        }
        //  bouton pour vider le log (génère une url avec debug_log_clear=1)
        echo '<p><a href="' . esc_url(admin_url('admin.php?page=debug-log-viewer&debug_log_clear=1')) . '" class="button">Vider le log</a></p>';
        echo '<p>';
        echo '<label for="log-search">Filtrer les lignes du log : </label>';
        echo '<input type="text" class="log-search" placeholder="Error..." style="min-width: 300px;">';
        echo '</p>';
        echo '<p><a href="" class="button btn-search">Rechercher dans le log</a></p>';
        echo '<section class="containerlog">';
            echo '<div class="pre_container">';
                echo '<pre style="background:#111; color:#0f0; padding:1rem; max-height:500px; overflow:auto;">';

                //  vérifie si debug.log existe et affiche son contenu 
                if (file_exists($this->log_file)) {
                    $log = file_get_contents($this->log_file);
                    echo esc_html($log);
                } else {
                    echo 'Le fichier debug.log est introuvable ou vide.';
                }

                    echo '</pre>';
            echo '<div>';
        echo '</section>';
        echo '</div>'; 
    }



}

