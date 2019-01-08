@pageHeader
    @slot('pretitle', 'Presentation')
    Create Theme
@endpageHeader

<section>
    <form action="{{ route('themes.store') }}" method="post" role="form">
        @csrf

        <div class="form-section">
            <div class="form-section-column-content">
                <div class="form-section-header">Theme Info</div>
                <p class="form-section-message">Lorem ipsum dolor sit amet consectetur adipisicing elit. Unde, ratione minus animi esse sit dicta, eos, atque omnis placeat enim tempora. Unde accusantium ad illo earum a sit saepe explicabo.</p>
            </div>

            <div class="flex-1">
                <field-wrapper
                    label="Name"
                    field-id="name"
                    name="name"
                >
                    <div class="field-group">
                        <input type="text" name="name" id="name" class="field">
                    </div>
                </field-wrapper>

                <field-wrapper
                    label="Location"
                    field-id="location"
                    name="location"
                >
                    <div class="field-group">
                        <div class="field-addon font-mono text-sm text-grey-dark">themes/</div>

                        <input type="text" name="location" id="location" class="field">
                    </div>
                </field-wrapper>

                <field-wrapper label="Credits" field-id="credits">
                    <div class="field-group">
                        <textarea name="credits" id="credits" rows="5" class="field"></textarea>
                    </div>
                </field-wrapper>
            </div>
        </div>

        <div class="form-section">
            <div class="form-section-column-content">
                <div class="form-section-header">Presentation</div>
                <p class="form-section-message">Set the presentation defaults you'd like to use for your theme.</p>
            </div>

            <div class="form-section-column-form">
                <field-wrapper
                    label="Auth Layout"
                    field-id="layout_auth"
                    name="layout_auth"
                >
                    <layout-picker name="layout_auth" type="auth"></layout-picker>
                </field-wrapper>

                <field-wrapper
                    label="Public Site Layout"
                    field-id="layout_public"
                    name="layout_public"
                >
                    <layout-picker name="layout_public" type="public"></layout-picker>
                </field-wrapper>

                <field-wrapper
                    label="Admin Site Layout"
                    field-id="layout_admin"
                    name="layout_admin"
                >
                    <layout-picker name="layout_admin" type="admin"></layout-picker>
                </field-wrapper>

                <field-wrapper
                    label="Icon Set"
                    field-id="icon_set"
                    name="icon_set"
                >
                    <icon-set-picker name="icon_set"></icon-set-picker>
                </field-wrapper>
            </div>
        </div>

        <div class="form-section">
            <div class="form-section-column-content">
                <div class="form-section-header">Scaffolding</div>
                <p class="form-section-message">When you create your theme, Nova will create all of the necessary directories and files for your theme. These options allow you to specify additional files you want created during scaffolding.</p>
            </div>

            <div class="form-section-column-form">
                <field-wrapper
                    label="Variants"
                    field-id="variants"
                    name="variants"
                    help="Enter the names of any variants you want for your theme, separated by commas."
                >
                    <div class="field-group">
                        <input type="text" name="variants" id="variants" class="field">
                    </div>
                </field-wrapper>
            </div>
        </div>

        <div class="form-controls">
            <button type="submit" class="button button-primary button-large">Create</button>

            <a href="{{ route('themes.index') }}" class="button button-large">Cancel</a>
        </div>
    </form>
</section>