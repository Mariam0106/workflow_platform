
<?php switch($name ?? ''):
    case ('dashboard'): ?>
        <svg viewBox="0 0 18 18" fill="none" class="<?php echo e($class ?? ''); ?>"><rect x="2.5" y="2.5" width="6" height="6" rx="1" stroke="currentColor" stroke-width="1.5"/><rect x="9.5" y="2.5" width="6" height="4" rx="1" stroke="currentColor" stroke-width="1.5"/><rect x="9.5" y="8.5" width="6" height="7" rx="1" stroke="currentColor" stroke-width="1.5"/><rect x="2.5" y="10.5" width="6" height="5" rx="1" stroke="currentColor" stroke-width="1.5"/></svg>
        <?php break; ?>
    <?php case ('inbox'): ?>
        <svg viewBox="0 0 18 18" fill="none" class="<?php echo e($class ?? ''); ?>"><path d="M2.5 9h3.8l1 2h3.4l1-2h3.8" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/><path d="M3.3 4.5h11.4a.8.8 0 01.78.63l1 4.6v4.3a1 1 0 01-1 1H2.5a1 1 0 01-1-1v-4.3l1-4.6a.8.8 0 01.78-.63z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/></svg>
        <?php break; ?>
    <?php case ('check'): ?>
        <svg viewBox="0 0 18 18" fill="none" class="<?php echo e($class ?? ''); ?>"><circle cx="9" cy="9" r="6.5" stroke="currentColor" stroke-width="1.5"/><path d="M6.2 9.2l1.8 1.8 3.8-3.8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
        <?php break; ?>
    <?php case ('bell'): ?>
        <svg viewBox="0 0 18 18" fill="none" class="<?php echo e($class ?? ''); ?>"><path d="M5 7.2a4 4 0 018 0c0 3.3 1.2 4.3 1.2 4.3H3.8S5 10.5 5 7.2z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/><path d="M7.5 13.5a1.5 1.5 0 003 0" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
        <?php break; ?>
    <?php case ('clock'): ?>
        <svg viewBox="0 0 18 18" fill="none" class="<?php echo e($class ?? ''); ?>"><circle cx="9" cy="9" r="6.5" stroke="currentColor" stroke-width="1.5"/><path d="M9 5.5V9l2.5 1.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
        <?php break; ?>
    <?php case ('users'): ?>
        <svg viewBox="0 0 18 18" fill="none" class="<?php echo e($class ?? ''); ?>"><circle cx="6.7" cy="6" r="2.2" stroke="currentColor" stroke-width="1.5"/><path d="M2.5 14.5c0-2 1.9-3.5 4.2-3.5s4.2 1.5 4.2 3.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><path d="M11.3 4a2.2 2.2 0 010 4.2M13 10.8c1.6.3 2.8 1.5 2.8 3.2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
        <?php break; ?>
    <?php case ('building'): ?>
        <svg viewBox="0 0 18 18" fill="none" class="<?php echo e($class ?? ''); ?>"><rect x="3.5" y="2.5" width="8" height="13" rx="0.5" stroke="currentColor" stroke-width="1.5"/><path d="M11.5 7.5h3v8h-3" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/><path d="M6 5.5h1.5M6 8h1.5M6 10.5h1.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
        <?php break; ?>
    <?php case ('layers'): ?>
        <svg viewBox="0 0 18 18" fill="none" class="<?php echo e($class ?? ''); ?>"><path d="M9 2.8l6 3.2-6 3.2-6-3.2 6-3.2z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/><path d="M3 9.2L9 12.4l6-3.2M3 12L9 15.2 15 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
        <?php break; ?>
    <?php case ('briefcase'): ?>
        <svg viewBox="0 0 18 18" fill="none" class="<?php echo e($class ?? ''); ?>"><rect x="2.5" y="5.5" width="13" height="9" rx="1" stroke="currentColor" stroke-width="1.5"/><path d="M6.5 5.5V4a1 1 0 011-1h3a1 1 0 011 1v1.5M2.5 9.5h13" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/></svg>
        <?php break; ?>
    <?php case ('file'): ?>
        <svg viewBox="0 0 18 18" fill="none" class="<?php echo e($class ?? ''); ?>"><path d="M5 2.5h5.5L13.5 5.5V15a.5.5 0 01-.5.5H5a.5.5 0 01-.5-.5V3a.5.5 0 01.5-.5z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/><path d="M6.5 9h5M6.5 11.5h5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
        <?php break; ?>
    <?php case ('branch'): ?>
        <svg viewBox="0 0 18 18" fill="none" class="<?php echo e($class ?? ''); ?>"><circle cx="4.5" cy="3.5" r="1.5" stroke="currentColor" stroke-width="1.5"/><circle cx="4.5" cy="14.5" r="1.5" stroke="currentColor" stroke-width="1.5"/><circle cx="13.5" cy="9" r="1.5" stroke="currentColor" stroke-width="1.5"/><path d="M4.5 5v8M4.5 9c0-2.5 2-3 5-3h2.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
        <?php break; ?>
    <?php case ('chart'): ?>
        <svg viewBox="0 0 18 18" fill="none" class="<?php echo e($class ?? ''); ?>"><path d="M3 15V6.5M8 15V3M13 15V9.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><path d="M2 15.5h14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
        <?php break; ?>
    <?php case ('settings'): ?>
        <svg viewBox="0 0 18 18" fill="none" class="<?php echo e($class ?? ''); ?>"><path d="M3 5.5h8M13.5 5.5h1.5M3 12.5h1.5M7 12.5h8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><circle cx="11" cy="5.5" r="1.7" stroke="currentColor" stroke-width="1.5"/><circle cx="5" cy="12.5" r="1.7" stroke="currentColor" stroke-width="1.5"/></svg>
        <?php break; ?>
    <?php case ('plus'): ?>
        <svg viewBox="0 0 18 18" fill="none" class="<?php echo e($class ?? ''); ?>"><path d="M9 3.5v11M3.5 9h11" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
        <?php break; ?>
    <?php case ('edit'): ?>
        <svg viewBox="0 0 18 18" fill="none" class="<?php echo e($class ?? ''); ?>"><path d="M11.5 2.8l3.7 3.7-8.7 8.7-4.3.6.6-4.3 8.7-8.7z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/></svg>
        <?php break; ?>
    <?php case ('archive'): ?>
        <svg viewBox="0 0 18 18" fill="none" class="<?php echo e($class ?? ''); ?>"><rect x="2.5" y="3" width="13" height="3.2" rx="0.6" stroke="currentColor" stroke-width="1.5"/><path d="M3.5 6.5v7a1 1 0 001 1h9a1 1 0 001-1v-7" stroke="currentColor" stroke-width="1.5"/><path d="M7.3 9.7h3.4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
        <?php break; ?>
    <?php case ('restore'): ?>
        <svg viewBox="0 0 18 18" fill="none" class="<?php echo e($class ?? ''); ?>"><path d="M3 5.5A6 6 0 119 15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><path d="M3 2.5v3.4h3.4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
        <?php break; ?>
    <?php case ('search'): ?>
        <svg viewBox="0 0 18 18" fill="none" class="<?php echo e($class ?? ''); ?>"><circle cx="8" cy="8" r="5" stroke="currentColor" stroke-width="1.5"/><path d="M15 15l-3.4-3.4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
        <?php break; ?>
    <?php case ('arrow-left'): ?>
        <svg viewBox="0 0 18 18" fill="none" class="<?php echo e($class ?? ''); ?>"><path d="M15 9H3M3 9l5-5M3 9l5 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
        <?php break; ?>
    <?php case ('mail'): ?>
        <svg viewBox="0 0 18 18" fill="none" class="<?php echo e($class ?? ''); ?>"><rect x="2.5" y="4" width="13" height="10" rx="1" stroke="currentColor" stroke-width="1.5"/><path d="M3 5l6 4.5L15 5" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/></svg>
        <?php break; ?>
    <?php case ('phone'): ?>
        <svg viewBox="0 0 18 18" fill="none" class="<?php echo e($class ?? ''); ?>"><path d="M4 2.5h2.2l1 3-1.4 1.3a8 8 0 004.4 4.4l1.3-1.4 3 1v2.2a1 1 0 01-1.1 1A11.5 11.5 0 013 3.6 1 1 0 014 2.5z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/></svg>
        <?php break; ?>
    <?php case ('chevron-down'): ?>
        <svg viewBox="0 0 18 18" fill="none" class="<?php echo e($class ?? ''); ?>"><path d="M4.5 7l4.5 4.5L13.5 7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
        <?php break; ?>
    <?php case ('shield'): ?>
        <svg viewBox="0 0 18 18" fill="none" class="<?php echo e($class ?? ''); ?>"><path d="M9 2.3l5.5 2v3.5c0 4-2.4 6.7-5.5 7.9-3.1-1.2-5.5-3.9-5.5-7.9V4.3L9 2.3z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/><path d="M6.3 9l1.9 1.9L11.9 7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
        <?php break; ?>
    <?php case ('close'): ?>
        <svg viewBox="0 0 18 18" fill="none" class="<?php echo e($class ?? ''); ?>"><path d="M4.5 4.5l9 9M13.5 4.5l-9 9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
        <?php break; ?>
    <?php case ('alert'): ?>
        <svg viewBox="0 0 18 18" fill="none" class="<?php echo e($class ?? ''); ?>"><path d="M9 2.5l7.2 12.5a1 1 0 01-.87 1.5H2.67a1 1 0 01-.87-1.5L9 2.5z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/><path d="M9 7.2v3.3M9 12.8h.01" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
        <?php break; ?>
    <?php case ('switch'): ?>
        <svg viewBox="0 0 18 18" fill="none" class="<?php echo e($class ?? ''); ?>"><path d="M3 6h9M9 6L6.5 3.5M9 6L6.5 8.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M15 12H6M9 12l2.5 2.5M9 12l2.5-2.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
        <?php break; ?>
    <?php case ('trash'): ?>
        <svg viewBox="0 0 18 18" fill="none" class="<?php echo e($class ?? ''); ?>"><path d="M3.5 5h11M7 5V3.5a1 1 0 011-1h2a1 1 0 011 1V5m-6.5 0l.6 9a1 1 0 001 .9h4.8a1 1 0 001-.9l.6-9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
        <?php break; ?>
    <?php case ('logout'): ?>
        <svg viewBox="0 0 18 18" fill="none" class="<?php echo e($class ?? ''); ?>"><path d="M7 15.5H4a1 1 0 01-1-1v-11a1 1 0 011-1h3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M12 12.5l3.5-3.5-3.5-3.5M15.5 9H6.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
        <?php break; ?>
<?php endswitch; ?>
<?php /**PATH C:\projects\to step 12 backup\resources\views/layouts/partials/icon.blade.php ENDPATH**/ ?>