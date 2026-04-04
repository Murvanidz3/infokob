<?php

declare(strict_types=1);

/** @var array<string, string> $meta */
/** @var string $pageH1 */
/** @var string $leadKey */
/** @var list<array{route:string,times:list<string>,note:string}> $marsh */
/** @var string $trainIntro */
/** @var list<array{dep:string,arr:string,train:string,note:string}> $trainRows */
?>
<div class="container guide-page">
    <p class="guide-demo-banner"><?= Helpers::e(Helpers::__('guide_demo_notice')) ?></p>
    <h1 class="page-title"><?= Helpers::e($pageH1) ?></h1>
    <p class="page-lead"><?= Helpers::e(Helpers::__($leadKey)) ?></p>

    <h2 class="guide-section-title"><?= Helpers::e(Helpers::__('guide_transport_marsh')) ?></h2>
    <?php foreach ($marsh as $m): ?>
        <section class="guide-transport-block">
            <h3 class="guide-transport-block__route"><?= Helpers::e($m['route']) ?></h3>
            <div class="guide-time-chips" role="list">
                <?php foreach ($m['times'] as $t): ?>
                    <span class="guide-time-chip" role="listitem"><?= Helpers::e($t) ?></span>
                <?php endforeach; ?>
            </div>
            <p class="guide-transport-note"><?= Helpers::e($m['note']) ?></p>
        </section>
    <?php endforeach; ?>

    <h2 class="guide-section-title"><?= Helpers::e(Helpers::__('guide_transport_train')) ?></h2>
    <p class="guide-train-intro"><?= Helpers::e($trainIntro) ?></p>
    <div class="guide-table-wrap">
        <table class="guide-table">
            <thead>
                <tr>
                    <th><?= Helpers::e(Helpers::__('guide_train_dep')) ?></th>
                    <th><?= Helpers::e(Helpers::__('guide_train_arr')) ?></th>
                    <th><?= Helpers::e(Helpers::__('guide_train_num')) ?></th>
                    <th><?= Helpers::e(Helpers::__('guide_train_duration')) ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($trainRows as $r): ?>
                    <tr>
                        <td><?= Helpers::e($r['dep']) ?></td>
                        <td><?= Helpers::e($r['arr']) ?></td>
                        <td><?= Helpers::e($r['train']) ?></td>
                        <td><?= Helpers::e($r['note']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
