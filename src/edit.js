import { __ } from "@wordpress/i18n";
import { useBlockProps } from "@wordpress/block-editor";
import { useSelect } from "@wordpress/data";
import { useState, useEffect, RawHTML } from "@wordpress/element";
import { format, dateI18n, getSettings } from "@wordpress/date";

export default function Edit({ attributes }) {
	const { displayFeaturedImage } = attributes;

	const [sortedPosts, setSortedPosts] = useState();

	// Fetch the top 5 most viewed posts
	const topMostViewedPosts = useSelect((select) => {
		return select("core").getEntityRecords("postType", "post", {
			per_page: 5, // Limit to 5 posts
			_embed: true,
		});
	});

	useEffect(() => {
		if (topMostViewedPosts) {
			const sortedPosts = topMostViewedPosts.sort((a, b) => {
				const viewCountA = a.meta && a.meta.post_views;
				const viewCountB = b.meta && b.meta.post_views;
				return viewCountB - viewCountA;
			});

			setSortedPosts(sortedPosts);
			console.log(sortedPosts);
		}
	}, [topMostViewedPosts]);

	return (
		<ul {...useBlockProps()}>
			{sortedPosts &&
				sortedPosts.map((post) => {
					const featuredImage =
						post._embedded &&
						post._embedded["wp:featuredmedia"] &&
						post._embedded["wp:featuredmedia"].length > 0 &&
						post._embedded["wp:featuredmedia"][0];
					return (
						<li key={post.id}>
							{displayFeaturedImage && featuredImage && (
								<img
									src={featuredImage.media_details.sizes.large.source_url}
									alt={featuredImage.alt_text}
								/>
							)}
							<h5>
								<a href={post.link}>
									{post.title.rendered ? (
										<RawHTML>{post.title.rendered}</RawHTML>
									) : (
										__("(No title)", "most-viewed-posts")
									)}
								</a>
							</h5>
							{post.date_gmt && (
								<time dateTime={format("c", post.date_gmt)}>
									{dateI18n(getSettings().formats.date, post.date_gmt)}
								</time>
							)}
							{post.excerpt.rendered && (
								<RawHTML>{post.excerpt.rendered}</RawHTML>
							)}
						</li>
					);
				})}
		</ul>
	);
}
