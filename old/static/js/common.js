let hwapiLocal = true;

/**
 * This is extracted into its own function because we need to call localhost instead of spec-ify.com if there is a
 * hwapi dev server currently running.
 *
 * The path should not start with a slash.
 */
export async function call_hwapi(path, payload, fallbackCallack = () => {}) {
    // https://stackoverflow.com/a/57949518
    const isLocalhost = Boolean(
        window.location.hostname === 'localhost' ||
        // [::1] is the IPv6 localhost address.
        window.location.hostname === '[::1]' ||
        // 127.0.0.0/8 is considered localhost for IPv4.
        window.location.hostname.match(
            /^127(?:\.(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)){3}$/
        )
    );

    let rawResponse;
    if (isLocalhost && hwapiLocal) {
        console.info("Trying local server for hwapi");

        try {
            if (payload) {
                rawResponse = await fetch(
                    `http://localhost:3000/${path}`,
                    {
                        method: "POST",
                        mode: "cors",
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(payload)
                    },
                );
            } else {
                rawResponse = await fetch(
                    `http://localhost:3000/${path}`,
                    {
                        method: "GET",
                        mode: "cors",
                        headers: {
                            'Accept': 'application/json'
                        }
                    },
                );
            }
        } catch (e) {
            fallbackCallack();
            console.warn("Hwapi dev server not online, falling back to spec-ify.com");
            hwapiLocal = false;
        }
    }
    if (!rawResponse) {
        if (payload) {
            rawResponse = await fetch(
                `https://spec-ify.com/${path}`,
                {
                    method: "POST",
                    mode: "cors",
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(payload)
                },
            );
        } else {
            rawResponse = await fetch(
                `https://spec-ify.com/${path}`,
                {
                    method: "GET",
                    mode: "cors",
                    headers: {
                        'Accept': 'application/json'
                    }
                },
            );
        }
    }

    try {
        return await rawResponse.json();
    } catch (e) {
        if (rawResponse.status !== 404) // prevent error spam
            console.error("Could not parse json from hwapi!");

        return {};
    }
}

export function createPcieHexId(id) {
    id = id.replace("0x", "").toUpperCase();
    const regex = /^[0-9A-F]{4}$/i;

    if (!regex.test(id)) {
        id = "0".repeat(4 - id.length) + id;
    }

    return id;
}
