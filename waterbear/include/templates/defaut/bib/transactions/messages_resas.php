

<?PHP print ($message);  ?>

<?PHP  if ($message != "") { ?>
<script language="javascript">
window.print();
</script>
<?PHP } else {
    print("Aucun courrier a imprimer");
}
?>