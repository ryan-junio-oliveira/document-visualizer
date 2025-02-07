<?php

use RyanJunioOliveira\DocumentVisualizer\DocumentViewer;

require('vendor/autoload.php');

$viewer = new DocumentViewer('docs/teste.docx');

?>

<div>
<?php echo $viewer->visualize(); ?>
</div>
