<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
?>

<div class="users-list" id="userList">

    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>ИМЯ</th>
            <th>ЛОГИН</th>
            <th>EMAIL</th>
        </tr>
        </thead>

        <tbody>
        <? foreach ($arResult['ITEMS'] as $item): ?>
            <tr>
                <td><?=$item['ID']?></td>
                <td><?=$item['NAME']?></td>
                <td><?=$item['LOGIN']?></td>
                <td><?=$item['EMAIL']?></td>
            </tr>
        <? endforeach; ?>
        </tbody>

    </table>

    <div class="users-list-pagination">
        <?=$arResult['NAV_STRING']?>
    </div>

</div>

<nav class="users-list-files">
    <? if ($arResult['XMLPath']): ?>
        <a href="<?=$arResult['XMLPath']?>" target="_blank" download="">XML</a>
    <? endif; ?>
    <? if ($arResult['CsvPath']): ?>
        <a href="<?=$arResult['CsvPath']?>" target="_blank" download="">CSV</a>
    <? endif; ?>
</nav>


<script>
    $(document).ready(function () {
        $(document).on('click','.users-list-pagination a',function(){
            var data = $(this).attr('href').split('?');
             $.ajax({
                 type: "POST",
                 url: self.location.href,
                 data: data[1]
             }).success(function( data ) {
                 var content = $(data).filter('.users-list').html();
                 $('#userList').html(content);
             });
            return false;
        });
    });
</script>