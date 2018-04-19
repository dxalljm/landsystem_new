<?php
namespace frontend\controllers;
?>
<style> table,td,th {border: 1px solid black;border-style: solid;border-collapse: collapse;}</style>
<table width="1050px" border="1">
    <tr>
        <td align="center" height="40px"><strong>农场名称</strong></td>
        <td align="center"><?= $farm['farmname']?></td>
        <td align="center"><strong>合同号</strong></td>
        <td align="center"><?= $farm['contractnumber']?></td>
        <td align="center"><strong>合同面积</strong></td>
        <td align="center"><?= $farm['contractarea']?>亩</td>
        <td align="center"><strong>农场位置</strong></td>
        <td><?= $farm['address']?></td>
    </tr>
    <tr>
        <td align="center" height="40px"><strong>法人姓名</strong></td>
        <td align="center"><?= $farm['farmername']?></td>
        <td align="center"><strong>身份证号</strong></td>
        <td align="center"><?= $farm['cardid']?></td>
        <td align="center"><strong>联系电话</strong></td>
        <td align="center"><?= $farm['telephone']?></td>
        <td align="center"><strong>农场坐标</strong></td>
        <td align="left">&nbsp;<?php if(!preg_match('/([0-9])/', $farm['longitude'])) echo 'E___°__\'__.__"'.'&nbsp;&nbsp;'.'N___°__\'__.__"'; else echo $farm['longitude'].'&nbsp;&nbsp;'.$farm['latitude']?></td>

    </tr>
</table>
