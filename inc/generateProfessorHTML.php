<?php

function generateProfessorHTML($id)
{
    $profPost = new WP_Query([
        'post_type' => 'professor',
        'p' => $id
    ]);

    while ($profPost->have_posts()) {
        $profPost->the_post();
        $professor = [
            'name' => get_the_title(),
            'thumbnail' => get_the_post_thumbnail_url(null, 'professor'),
            'description' => get_the_content(),
            'email' => get_post_meta($id, 'email', true), // get_field('email')
            'twitter' => get_post_meta($id, 'twitter', true),
            'facebook' => get_post_meta($id, 'facebook', true)
        ];

        ob_start(); ?>

        <div class="professor_callout flex bg-gray-300 rounded-md items-center h-[235px] min-h-[235px]">
            <div class="professor-card__image w-1/4 bg-slate-400 h-full rounded-l-md bg-cover" style="background-image: url('<?php echo $professor['thumbnail'] ?>');"></div>
            <div class="professor-card__content w-3/4 p-4">
                <h2 class="professor_title"><?php echo esc_html($professor['name']); ?></h2>
                <p class="text-sm"><?php echo custom_trim_content($professor['description'], 30, 'Read More'); ?></p>
                <ul class="flex gap-4 list-style_none">
                    <?php if ($professor['email']) { ?>
                        <li><a href="mailto:<?php echo esc_html($professor['email']); ?>">Email</a></li>
                    <?php } ?>
                    <?php if ($professor['twitter']) { ?>
                        <li><a href="<?php echo esc_html($professor['twitter']); ?>">Twitter</a></li>
                    <?php } ?>
                    <?php if ($professor['facebook']) { ?>
                        <li><a href="<?php echo esc_html($professor['facebook']); ?>">Facebook</a></li>
                    <?php } ?>
                </ul>
            </div>
        </div>

<?php return ob_get_clean();
    }
}
