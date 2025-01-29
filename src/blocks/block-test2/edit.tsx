import { __ } from "@wordpress/i18n";
import {
	InspectorControls,
	MediaUpload,
	MediaUploadCheck,
	MediaPlaceholder,
} from "@wordpress/block-editor";
import { PanelBody, ColorPalette, Button } from "@wordpress/components";
import { ImagePlaceholder } from "../../images";
import { ComponentWrapper, ComponentPropsEdit } from "./Component";

const colors = [
	{ name: "black", color: "#000" },
	{ name: "white", color: "#fff" },
	{ name: "red", color: "#f00" },
	{ name: "green", color: "#0f0" },
	{ name: "blue", color: "#00f" },
];

const Edit: ComponentPropsEdit = (props) => {
	const { heading, headingColor, content, contentColor, image } =
		props.attributes;
	let imageUrl = image !== "" ? image : ImagePlaceholder;
	const ALLOWED_MEDIA_TYPES = ["image"];
	// const [counter, setCounter] = useState(0);
	return (
		<>
			<InspectorControls>
				<PanelBody
					title={__("Image Picker", "example-gutenberg-blocks")}
					initialOpen={true}
				>
					<MediaUploadCheck>
						<MediaUpload
							onSelect={(media) => props.setAttributes({ image: media.url })}
							allowedTypes={ALLOWED_MEDIA_TYPES}
							value={image as any}
							render={({ open }) => (
								<Button className="is-primary" onClick={open}>
									Open Media Library
								</Button>
							)}
						/>
					</MediaUploadCheck>
					<MediaPlaceholder
						onSelect={(media) => props.setAttributes({ image: media.url })}
						allowedTypes={["image"]}
						multiple={false}
						labels={{ title: "The Image" }}
					></MediaPlaceholder>

					<img className="feature-icon" src={imageUrl} alt="feature-icon" />
				</PanelBody>
				<PanelBody
					title={__("Typography", "example-gutenberg-blocks")}
					initialOpen={false}
				>
					<p className="custom__editor__label">
						{__("Title Color", "example-gutenberg-blocks")}
					</p>
					<ColorPalette
						colors={colors}
						value={headingColor}
						onChange={(newColor) =>
							props.setAttributes({ headingColor: newColor })
						}
					/>
					<p className="custom__editor__label">
						{__("Content Color", "example-gutenberg-blocks")}
					</p>
					<ColorPalette
						colors={colors}
						value={contentColor}
						onChange={(newColor) =>
							props.setAttributes({ contentColor: newColor })
						}
					/>
				</PanelBody>
			</InspectorControls>
			<div className="mt-block-test2-wrapper">
				<ComponentWrapper edit {...props} />
			</div>
		</>
	);
};

export default Edit;
