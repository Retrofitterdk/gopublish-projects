import { registerPlugin } from '@wordpress/plugins';
import { PluginDocumentSettingPanel } from '@wordpress/editor';
import { TextControl } from '@wordpress/components';
import { useEntityProp } from '@wordpress/core-data';
import { useSelect } from '@wordpress/data';
import { store as editorStore } from '@wordpress/editor';
import { __ } from '@wordpress/i18n';

const ProjectDetailsPanel = () => {
	const postType = useSelect(
		( select ) => select( editorStore ).getCurrentPostType(),
		[]
	);
	const [ meta, setMeta ] = useEntityProp( 'postType', postType, 'meta' );

	if ( ! meta ) {
		return null;
	}

	const setField = ( key ) => ( value ) => setMeta( { ...meta, [ key ]: value } );

	return (
		<PluginDocumentSettingPanel
			name="ccp-project-details"
			title={ __( 'Project Details', 'gopublish-projects' ) }
			icon="portfolio"
		>
			<TextControl
				__nextHasNoMarginBottom
				label={ __( 'URL', 'gopublish-projects' ) }
				help={ __( 'The URL of the project website or page.', 'gopublish-projects' ) }
				type="url"
				value={ meta.url || '' }
				onChange={ setField( 'url' ) }
			/>
			<TextControl
				__nextHasNoMarginBottom
				label={ __( 'Client', 'gopublish-projects' ) }
				help={ __( 'The name of the client the project was built for.', 'gopublish-projects' ) }
				value={ meta.client || '' }
				onChange={ setField( 'client' ) }
			/>
			<TextControl
				__nextHasNoMarginBottom
				label={ __( 'Location', 'gopublish-projects' ) }
				help={ __( 'The physical location where the project took place.', 'gopublish-projects' ) }
				value={ meta.location || '' }
				onChange={ setField( 'location' ) }
			/>
			<TextControl
				__nextHasNoMarginBottom
				label={ __( 'Start Date', 'gopublish-projects' ) }
				type="date"
				value={ meta.start_date || '' }
				onChange={ setField( 'start_date' ) }
			/>
			<TextControl
				__nextHasNoMarginBottom
				label={ __( 'End Date', 'gopublish-projects' ) }
				type="date"
				value={ meta.end_date || '' }
				onChange={ setField( 'end_date' ) }
			/>
		</PluginDocumentSettingPanel>
	);
};

registerPlugin( 'ccp-project-details', { render: ProjectDetailsPanel } );
