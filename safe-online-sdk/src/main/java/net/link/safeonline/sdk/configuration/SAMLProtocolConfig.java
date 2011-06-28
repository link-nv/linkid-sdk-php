package net.link.safeonline.sdk.configuration;

import net.link.safeonline.sdk.auth.protocol.saml2.SAMLBinding;
import net.link.util.config.Group;
import net.link.util.config.Property;


/**
 * <h2>{@link SAMLProtocolConfig}<br>
 * <sub>[in short] (TODO).</sub></h2>
 *
 * <p>
 * <i>09 16, 2010</i>
 * </p>
 *
 * @author lhunath
 */
@Group(prefix = "saml")
public interface SAMLProtocolConfig {

    /**
     * Resource path to a custom velocity template to build the browser POST that contains the SAML2 ticket.
     *
     * <i>[required, default: A built-in template]</i>
     */
    @Property(required = true, unset = "/net/link/safeonline/sdk/auth/saml2/saml2-post-binding.vm")
    String postBindingTemplate();

    /**
     * SAML2 binding to use when dispatching requests. See {@link SAMLBinding} for possible values.
     *
     * <i>[required, default: HTTP_POST]</i>
     */
    @Property( required = true, unset = "HTTP_POST")
    SAMLBinding binding();

    /**
     * Saml2 Relay State parameter.
     *
     * <i>[optional, default: don't pass any relay state]</i>
     */
    @Property(required = false)
    String relayState();

    /**
     * Indiciates whether the returned SAML 2 Browser Post Form should break out of its frame ( target=_top )
     *
     * <i>[optional, default: false]</i>
     */
    @Property(required = false, unset = "false")
    Boolean breakFrame();
}
