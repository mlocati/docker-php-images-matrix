[![Check PHP Docker Images](https://github.com/mlocati/docker-php-images-matrix/actions/workflows/update.yml/badge.svg)](https://github.com/mlocati/docker-php-images-matrix/actions/workflows/update.yml)

List of docker images and corresponding OS versions available on Docker Hub.

## The Matrix

<!-- START OF DOCKER IMAGE MATRIX -->
| PHP Version | OS Versions |
|:---:|:---|
| 8.5<br />(last: 8.5.2) | <ul><li>bookworm</li><li>trixie[*](#is-default-legend)</li><li>alpine3.21 (stuck at 8.5.0)</li><li>alpine3.22</li><li>alpine3.23[*](#is-default-legend)</li></ul> |
| 8.4<br />(last: 8.4.17) | <ul><li>bullseye (stuck at 8.4.11)</li><li>bookworm</li><li>trixie[*](#is-default-legend)</li><li>alpine3.19 (stuck at 8.4.1)</li><li>alpine3.20 (stuck at 8.4.8)</li><li>alpine3.21 (stuck at 8.4.15)</li><li>alpine3.22</li><li>alpine3.23[*](#is-default-legend)</li></ul> |
| 8.3<br />(last: 8.3.30) | <ul><li>bullseye (stuck at 8.3.24)</li><li>bookworm</li><li>trixie[*](#is-default-legend)</li><li>alpine3.17 (stuck at 8.3.0)</li><li>alpine3.18 (stuck at 8.3.7)</li><li>alpine3.19 (stuck at 8.3.14)</li><li>alpine3.20 (stuck at 8.3.22)</li><li>alpine3.21 (stuck at 8.3.28)</li><li>alpine3.22</li><li>alpine3.23[*](#is-default-legend)</li></ul> |
| 8.2<br />(last: 8.2.30) | <ul><li>buster (stuck at 8.2.7)</li><li>bullseye (stuck at 8.2.29)</li><li>bookworm</li><li>trixie[*](#is-default-legend)</li><li>alpine3.16 (stuck at 8.2.5)</li><li>alpine3.17 (stuck at 8.2.13)</li><li>alpine3.18 (stuck at 8.2.19)</li><li>alpine3.19 (stuck at 8.2.26)</li><li>alpine3.20 (stuck at 8.2.28)</li><li>alpine3.21 (stuck at 8.2.29)</li><li>alpine3.22</li><li>alpine3.23[*](#is-default-legend)</li></ul> |
| 8.1<br />(last: 8.1.34) | <ul><li>buster (stuck at 8.1.20)</li><li>bullseye (stuck at 8.1.33)</li><li>bookworm</li><li>trixie[*](#is-default-legend)</li><li>alpine3.14 (stuck at 8.1.6)</li><li>alpine3.15 (stuck at 8.1.13)</li><li>alpine3.16 (stuck at 8.1.26)</li><li>alpine3.17 (stuck at 8.1.26)</li><li>alpine3.18 (stuck at 8.1.28)</li><li>alpine3.19 (stuck at 8.1.31)</li><li>alpine3.20 (stuck at 8.1.32)</li><li>alpine3.21[*](#is-default-legend)</li><li>alpine3.22</li></ul> |
| 8.0<br />(last: 8.0.30) | <ul><li>buster</li><li>bullseye[*](#is-default-legend)</li><li>alpine3.12 (stuck at 8.0.7)</li><li>alpine3.13 (stuck at 8.0.13)</li><li>alpine3.14 (stuck at 8.0.19)</li><li>alpine3.15 (stuck at 8.0.26)</li><li>alpine3.16[*](#is-default-legend)</li></ul> |
| 7.4<br />(last: 7.4.33) | <ul><li>buster</li><li>bullseye[*](#is-default-legend)</li><li>alpine3.10 (stuck at 7.4.6)</li><li>alpine3.11 (stuck at 7.4.14)</li><li>alpine3.12 (stuck at 7.4.20)</li><li>alpine3.13 (stuck at 7.4.26)</li><li>alpine3.14 (stuck at 7.4.29)</li><li>alpine3.15</li><li>alpine3.16[*](#is-default-legend)</li></ul> |
| 7.3<br />(last: 7.3.33) | <ul><li>stretch (stuck at 7.3.29)</li><li>buster</li><li>bullseye[*](#is-default-legend)</li><li>alpine3.8 (stuck at 7.3.6)</li><li>alpine3.9 (stuck at 7.3.13)</li><li>alpine3.10 (stuck at 7.3.18)</li><li>alpine3.11 (stuck at 7.3.26)</li><li>alpine3.12 (stuck at 7.3.28)</li><li>alpine3.13</li><li>alpine3.14</li><li>alpine3.15[*](#is-default-legend)</li></ul> |
| 7.2<br />(last: 7.2.34) | <ul><li>stretch</li><li>buster[*](#is-default-legend)</li><li>alpine3.6 (stuck at 7.2.11)</li><li>alpine3.7 (stuck at 7.2.11)</li><li>alpine3.8 (stuck at 7.2.19)</li><li>alpine3.9 (stuck at 7.2.26)</li><li>alpine3.10 (stuck at 7.2.31)</li><li>alpine3.11</li><li>alpine3.12[*](#is-default-legend)</li></ul> |
| 7.1<br />(last: 7.1.33) | <ul><li>jessie (stuck at 7.1.30)</li><li>stretch</li><li>buster[*](#is-default-legend)</li><li>alpine3.4 (stuck at 7.1.17)</li><li>alpine3.7 (stuck at 7.1.23)</li><li>alpine3.8 (stuck at 7.1.30)</li><li>alpine3.9</li><li>alpine3.10[*](#is-default-legend)</li></ul> |
| 7.0<br />(last: 7.0.33) | <ul><li>jessie</li><li>stretch[*](#is-default-legend)</li><li>alpine3.4 (stuck at 7.0.30)</li><li>alpine3.7[*](#is-default-legend)</li></ul> |
| 5.6<br />(last: 5.6.40) | <ul><li>jessie</li><li>stretch[*](#is-default-legend)</li><li>alpine3.4 (stuck at 5.6.36)</li><li>alpine3.7 (stuck at 5.6.38)</li><li>alpine3.8[*](#is-default-legend)</li></ul> |
| 5.4<br />(last: 5.4.45) | <ul><li>jessie[*](#is-default-legend)</li></ul> |
| 5.3<br />(last: 5.3.29) | <ul><li>jessie[*](#is-default-legend)</li></ul> |

Last updated: <time datetime="2026-01-27T08:26:30+00:00">2026-01-27 08:26:30 +00:00</time>
<!-- END OF DOCKER IMAGE MATRIX -->

> [!NOTE]
> <a name="is-default-legend"></a>[*] Default Debian/Alpine image for the specific PHP major.minor version
